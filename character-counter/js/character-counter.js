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
        
        //set up counter for repeatable fields
        let repeatable = document.getElementById('f_' + my_counter.repeatable); 
        let my_selector = '.k_element_' + my_counter.editable + ' input';
        var targets = repeatable.querySelectorAll(my_selector);
        //instantiate counter
        for (let target of targets){
            my_counter.target = target;
            instantiateCounter(my_counter);
        }
    }else{
        //set up counter for editable field or front-end field    
        let field_name = (my_counter.field) || "f_" + my_counter.editable;
        my_counter.target = document.getElementById(field_name);
        instantiateCounter(my_counter);
    }
}

function instantiateCounter(my_counter) {
        my_counter.id = my_counter.target.id + "_counter";
            
        //remove a counter if it already exists (for dynamic repeatable regions)
        if(document.getElementById(my_counter.id)){
            document.getElementById(my_counter.id).remove();
        }
        //instantiate counter
        let counter = document.createElement('p');
        counter.setAttribute('id', my_counter.id);
        counter.style.textAlign = 'right';
        my_counter.target.parentNode.appendChild(counter);
        
        startCounter(my_counter);
}

function startCounter(my_counter){
    //Add keyboard listener to field
    my_counter.target.addEventListener('keyup', function () {
        updateCounter(my_counter);
    });
    my_counter.target.addEventListener('keydown', function () {
        updateCounter(my_counter);
    });
    
    //set up initial character count
    updateCounter(my_counter);

}
        
function updateCounter(my_counter) {
    let counter = document.getElementById(my_counter.id);
    if (my_counter.type == 'up'){
        var count = my_counter.target.value.length; 
    }else{
        var count = my_counter.max - my_counter.target.value.length;
    }
    counter.innerHTML = '<span>' + count + '</span>';

    //Counter styles
    if (my_counter.type == 'up'){
        //count up
        if ((my_counter.max && count > my_counter.max) || count < my_counter.min){
            counter.children[0].style.color = 'red';
        }else{
            counter.children[0].style.color = 'green';
        }
    }else{ //count down
        if (count < my_counter.min) {
            counter.children[0].style.color = 'red';
        }else{
            counter.children[0].style.color = 'green';
        }       
    }
    //Show min and/or max
    if (my_counter.min && (my_counter.show.indexOf('min') >= 0 || my_counter.show.indexOf('both') >= 0)) {
        //show min
        counter.innerHTML = 'Min: '+  my_counter.min + '&nbsp;&nbsp;&nbsp;' + counter.innerHTML;
            }
    if (my_counter.max && (my_counter.show.indexOf('max') >= 0 || my_counter.show.indexOf('both') >= 0)) {
        //show max
        counter.innerHTML = counter.innerHTML + '&nbsp;&nbsp;&nbsp;Max: ' + my_counter.max;
    }
    counter.innerHTML = my_counter.label + '&nbsp;' + counter.innerHTML;
}

my_counters.forEach(initCounter);
