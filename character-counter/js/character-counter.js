function sanitize(my_counter) {
    my_counter.field = (!my_counter.field) ? '' : my_counter.field.trim();
    my_counter.repeatable = (!my_counter.repeatable) ? '' : my_counter.repeatable.trim();
    my_counter.editable = (!my_counter.editable) ? '' : my_counter.editable.trim();
    my_counter.max = (!my_counter.max) ? '' : parseInt(my_counter.max);
    my_counter.min = (!my_counter.min) ? '' : parseInt(my_counter.min);
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
        //instantiate counters
        for (let target of targets){
            let repeatable_counter = {
                max: my_counter.max,
                min: my_counter.min,
                type: my_counter.type,
                show: my_counter.show,
                label: my_counter.label,
                target: target
            };
            instantiateCounter(repeatable_counter);
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
            
    //create new counter element
    let new_counter = document.createElement('p');
    new_counter.setAttribute('id', my_counter.id);
    new_counter.style.textAlign = 'right';
    my_counter.target.parentNode.appendChild(new_counter);
        
    //Add keyboard listener to target field
    my_counter.target.addEventListener('keyup', function () {
        refreshCount(my_counter);
    });
    my_counter.target.addEventListener('keydown', function () {
        refreshCount(my_counter);
    });
    
    //set up initial character count
    buildCounter(my_counter);

}
        
function buildCounter(my_counter) {
    refreshCount(my_counter);
    
    //Show min and/or max
    if (my_counter.min && (my_counter.show.indexOf('min') >= 0 || my_counter.show.indexOf('both') >= 0)) {
        //show min
        current_counter.innerHTML = 'Min: '+  my_counter.min + '&nbsp;&nbsp;&nbsp;' + current_counter.innerHTML;
            }
    if (my_counter.max && (my_counter.show.indexOf('max') >= 0 || my_counter.show.indexOf('both') >= 0)) {
        //show max
        current_counter.innerHTML = current_counter.innerHTML + '&nbsp;&nbsp;&nbsp;Max: ' + my_counter.max;
    }
    //show label
    current_counter.innerHTML = my_counter.label + '&nbsp;' + current_counter.innerHTML;
}

function refreshCount(my_counter) {
    let current_counter = document.getElementById(my_counter.id);
    if (my_counter.type == 'up'){
        my_counter.count = my_counter.target.value.length; 
    }else{
        my_counter.count = my_counter.max - my_counter.target.value.length;
    }
    current_counter.innerHTML = '<span>' + my_counter.count + '</span>';

    //Counter styles
    if (my_counter.type == 'up'){
        //count up
        if ((my_counter.max && my_counter.count > my_counter.max) || my_counter.count < my_counter.min){
            current_counter.children[0].style.color = 'red';
        }else{
            current_counter.children[0].style.color = 'green';
        }
    }else{ //count down
        if (my_counter.count < my_counter.min) {
            current_counter.children[0].style.color = 'red';
        }else{
            current_counter.children[0].style.color = 'green';
        }       
    }
    return current_counter;
}

my_counters.forEach(initCounter);
