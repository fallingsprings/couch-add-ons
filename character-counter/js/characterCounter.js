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
            
    // If it's in a repeatable, switch to that function instead
    if (my_counter.repeatable) { repeatable(my_counter); return; }
            
    var field_name = (my_counter.field) || "f_" + my_counter.editable;
    
    var target = document.getElementById(field_name);
    //add a character counter beneath the field  
    var counter = document.createElement('p');
    var counter_id = field_name + "_counter";
    counter.setAttribute('id', counter_id);
    counter.style.textAlign = 'right';
    target.parentNode.appendChild(counter);
            
    startCounter(field_name, target, my_counter);
}

function repeatable(my_counter) {
    //listener to add counter when a new row is added
    var add_row_button = document.getElementById('addRow_f_' + my_counter.repeatable);
    if (add_row_button && add_row_button.addEventListener) {
        add_row_button.addEventListener('click', function () {
            initCounter(my_counter);
        });
    } else if (add_row_button && add_row_button.attachEvent) {
        add_row_button.attachEvent('onclick', function () {
            initCounter(my_counter);
        });
    }
            
    //create counters, looping through repeatable items
    for (var i = 0;; i++) {                
        field_name = "f_";
        field_name += my_counter.repeatable + "-" + i + "-";
        field_name += my_counter.editable;
        target = document.getElementById(field_name);

        //break the loop when there are no more
        if(target == null) { break; }
        //skip it if it's already there (when adding rows to a repeatable)
        if (document.getElementById(field_name + '_counter') != null) { continue; }
                
        //add a character counter beneath the field  
        var counter = document.createElement('p');
        var counter_id = field_name + "_counter";
        counter.setAttribute('id', counter_id);
        counter.style.textAlign = 'right';
        target.parentNode.appendChild(counter);
                
        startCounter(field_name, target, my_counter);                
    }
}
        
function startCounter(field_name, target, my_counter){
    //initiate character count
    updateCounter(field_name, my_counter.max, my_counter.min, my_counter.type, my_counter.show, my_counter.label);
            
    //add listener to input tag             
    target.setAttribute('onkeydown', "updateCounter('"+field_name+"', '"+my_counter.max+"', '"+my_counter.min+"', '"+my_counter.type+"', '"+my_counter.show+"', '"+my_counter.label+"');");
    target.setAttribute('onkeyup', "updateCounter('"+field_name+"', '"+my_counter.max+"', '"+my_counter.min+"', '"+my_counter.type+"', '"+my_counter.show+"', '"+my_counter.label+"');");

}
        
function updateCounter(field_name, max, min, type, show, label) {
    var target = document.getElementById(field_name);
    var counter = document.getElementById(field_name + '_counter');
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
