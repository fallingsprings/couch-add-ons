function sanitize(my_counter) {
    my_counter.field = (!my_counter.field) ? '' : my_counter.field.trim();
    my_counter.repeatable = (!my_counter.repeatable) ? '' : my_counter.repeatable.trim();
    my_counter.editable = (!my_counter.editable) ? '' : my_counter.editable.trim();
    my_counter.max = (!my_counter.max) ? '' : parseInt(my_counter.max);
    my_counter.min = (!my_counter.min) ? '' : parseInt(my_counter.min);
    my_counter.count = (!my_counter.count) ? '' : my_counter.count.trim().toLowerCase();
    my_counter.type = (!my_counter.type) ? '' : my_counter.type.trim().toLowerCase();
    my_counter.show = (!my_counter.show) ? '' : my_counter.show.trim().toLowerCase();
    my_counter.label = (!my_counter.label) ? '' : my_counter.label;
    //can't count down from nothing
    if (my_counter.max === '') { my_counter.type = 'up'; }
            
    return my_counter;
}
function initCounter(my_counter) {
    sanitize(my_counter);
            
    // Repeatable
    if (my_counter.repeatable) {
        //listener to add counter when a new row is added
        var add_row_button = document.getElementById('addRow_f_' + my_counter.repeatable);
        add_row_button.addEventListener('click', function () {
                initCounter(my_counter);
            });
        
        //target repeatable fields
        let repeatable = document.getElementById('f_' + my_counter.repeatable); 
        let my_selector = '.k_element_' + my_counter.editable + ' input';
        var targets = [].slice.call(repeatable.querySelectorAll(my_selector));
    }else{
        //target an editable field or front-end field    
        let field_name = (my_counter.field) || "f_" + my_counter.editable;
        var targets = [document.getElementById(field_name)];
    }
   
    //create character counter(s)
    targets.forEach(target => {
        let counter_id = target.id + "_counter";
            
        //remove a counter if it already exists (for dynamic repeatable regions)
        if(document.getElementById(counter_id)){
            document.getElementById(counter_id).remove();
        }
        //instantiate counter
        let counter = document.createElement('p');
        counter.setAttribute('id', counter_id);
        counter.style.textAlign = 'right';
        target.parentNode.appendChild(counter);
        
        startCounter(target, counter_id, my_counter);
    });
}
        
function startCounter(target, counter_id, my_counter){
    //set up initial character count
    updateCounter(target, counter_id, my_counter.max, my_counter.min, my_counter.type, my_counter.show, my_counter.label);

    //Add keyboard listener to field
        target.addEventListener('keyup', function () {
            updateCounter(target, counter_id, my_counter.max, my_counter.min, my_counter.type, my_counter.show, my_counter.label);
        });
        target.addEventListener('keydown', function () {
            updateCounter(target, counter_id, my_counter.max, my_counter.min, my_counter.type, my_counter.show, my_counter.label);
        });
}
        
function updateCounter(target, counter_id, max, min, type, show, label) {
    let counter = document.getElementById(counter_id);
    if (type == 'up'){
        var count = target.value.length; 
    }else{
        var count = max - target.value.length;
    }
    counter.innerHTML = '<span>' + count + '</span>';

    //Counter styles
    if (type == 'up'){
        //count up
        if ((max && count > max) || count < min){
            counter.children[0].style.color = 'red';
        }else{
            counter.children[0].style.color = 'green';
        }
    }else{ //count down
        if (count < min) {
            counter.children[0].style.color = 'red';
        }else{
            counter.children[0].style.color = 'green';
        }       
    }
    //Show min and/or max
    if (min && (show.indexOf('min') >= 0 || show.indexOf('both') >= 0)) {
        //show min
        counter.innerHTML = 'Min: '+  min + '&nbsp;&nbsp;&nbsp;' + counter.innerHTML;
            }
    if (max && (show.indexOf('max') >= 0 || show.indexOf('both') >= 0)) {
        //show max
        counter.innerHTML = counter.innerHTML + '&nbsp;&nbsp;&nbsp;Max: ' + max;
    }
    counter.innerHTML = label + '&nbsp;' + counter.innerHTML;
}

my_counters.forEach(initCounter);
