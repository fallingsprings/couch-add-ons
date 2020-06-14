function counterLocalization(){
    const LANG = {
        warning: 'Maximum Length',
        max: 'Max: ',
        min: 'Min: '
    }
    return LANG;
}

function sanitize(my_counter) {
    my_counter.field = (!my_counter.field) ? '' : my_counter.field.trim();
    my_counter.repeatable = (!my_counter.repeatable) ? '' : my_counter.repeatable.trim();
    my_counter.editable = (!my_counter.editable) ? '' : my_counter.editable.trim();
    my_counter.max = (!my_counter.max) ? '' : parseInt(my_counter.max);
    my_counter.min = (!my_counter.min) ? '' : parseInt(my_counter.min);
    my_counter.type = (!my_counter.type) ? '' : my_counter.type.trim().toLowerCase();
    my_counter.show = (!my_counter.show) ? '' : my_counter.show.trim().toLowerCase();
    my_counter.label = (!my_counter.label) ? '' : my_counter.label;
    my_counter.enforce_max = (!my_counter.enforce_max) ? false : true;
    my_counter.count = 0;
    //can't count down from nothing
    if (my_counter.max === '') { my_counter.type = 'up'; }
    
    initCounter(my_counter);
}
function initCounter(my_counter) {
    if (!my_counter.repeatable) {
        //regular static fields
        //instantiate counter for editable field or front-end field    
        let field_name = (my_counter.field) || `f_${my_counter.editable}`;
        my_counter.target = document.getElementById(field_name);
        instantiateCounter(my_counter);
    }else{
        //dynamic repeatable regions
        //identify all repeatable fields
        let repeatable = document.getElementById(`f_${my_counter.repeatable}`); 
        let my_selector = `.k_element_${my_counter.editable} input`;
        let targets = repeatable.querySelectorAll(my_selector);

        //edge case for initially empty repeatable tables        
        if(!targets.length){
           //mutation observer instantiates new repeatable field when it is added dynamically
            const repeatable_observer = new MutationObserver(function() {
            let targets = repeatable.querySelectorAll(my_selector);
            let target = targets.item(0);
                initRepeatable(my_counter, target);
                repeatable_observer.disconnect();
            });
            repeatable_observer.observe(repeatable, {subtree: true, childList: true});
           }
        
        //instantiate counters
        for (let target of targets){
            initRepeatable(my_counter, target);
        }
        
        //listener to add counter when a new row is added
        let add_row_button = document.getElementById(`addRow_f_${my_counter.repeatable}`);
        add_row_button.addEventListener('click', function () {
            //identify the new row and instantiate counter
            let targets = repeatable.querySelectorAll(my_selector);
            let target = targets.item(targets.length - 1);
            initRepeatable(my_counter, target);
        });        
    }
}

function initRepeatable(my_counter, target){
    let repeatable_counter = {
        max: my_counter.max,
        min: my_counter.min,
        type: my_counter.type,
        show: my_counter.show,
        label: my_counter.label,
        enforce_max: my_counter.enforce_max,
        count: 0,
        target: target
    };
    instantiateCounter(repeatable_counter);
}

function instantiateCounter(my_counter) {
    const LANG = counterLocalization();
    
    //Add keyboard listener to target field
    my_counter.target.addEventListener('keyup', function () {
        refreshCount(my_counter);
    });
    my_counter.target.addEventListener('keydown', function () {
        refreshCount(my_counter);
    });
            
    //create new counter element
    my_counter.id = `${my_counter.target.id}_counter`;
    let new_counter = document.createElement('p');
    new_counter.setAttribute('id', my_counter.id);
    new_counter.style.textAlign = 'right';
    my_counter.target.parentNode.appendChild(new_counter);
        
    //create dynamic span for count
    my_counter.element = document.getElementById(my_counter.id);
    my_counter.element.innerHTML = `<span>${my_counter.count}</span>`;
    
    //create static text
    //show min
    if (my_counter.min && (my_counter.show.indexOf('min') >= 0 || my_counter.show.indexOf('both') >= 0)) {
        my_counter.element.innerHTML = `${LANG.min} ${my_counter.min} &nbsp; ${my_counter.element.innerHTML}`;
            }
    //show max
    if (my_counter.max && (my_counter.show.indexOf('max') >= 0 || my_counter.show.indexOf('both') >= 0)) {
        my_counter.element.innerHTML = `${my_counter.element.innerHTML}&nbsp;&nbsp;&nbsp;${LANG.max} ${my_counter.max}`;
    }
    //show label
    my_counter.element.innerHTML = `${my_counter.label} ${my_counter.element.innerHTML}`;
    
    //create dynamic span for warning
    if(my_counter.enforce_max){
        my_counter.element.innerHTML = `<span style="color:red;">${LANG.warning}</span> ${my_counter.element.innerHTML}`;
    }
    
    //identify counter span and warning span
    my_counter.warning = (!my_counter.enforce_max) ? null : my_counter.element.children[0];
    my_counter.counter = (my_counter.enforce_max) ? my_counter.element.children[1] : my_counter.element.children[0];
    
    refreshCount(my_counter);  
}

function refreshCount(my_counter) {
    //enforce maximum character count
    if (my_counter.enforce_max){
        if( my_counter.target.value.length >= my_counter.max){
            my_counter.target.value = my_counter.target.value.slice(0, my_counter.max);
            //show/hide warning
            my_counter.warning.style.visibility = 'visible';
        }else{
            my_counter.warning.style.visibility = 'hidden';
        }
    }
    
    //calculate count
    if (my_counter.type === 'up'){
        my_counter.count = my_counter.target.value.length; 
    }else{
        my_counter.count = my_counter.max - my_counter.target.value.length;
    }
    my_counter.counter.innerHTML = my_counter.count;
    
    //Counter styles
    if (my_counter.type === 'up'){
        //count up
        if ((my_counter.max && my_counter.count > my_counter.max) || my_counter.count < my_counter.min){
            my_counter.counter.style.color = 'red';
        }else if (my_counter.max && my_counter.count > my_counter.max * .9){
            my_counter.counter.style.color = 'darkorange';
        }else{
            my_counter.counter.style.color = 'green';
        }
    }else{ 
        //count down
        if (my_counter.min && my_counter.target.value.length < my_counter.min || my_counter.count < 0) {
            my_counter.counter.style.color = 'red';
        }else if(my_counter.target.value.length > my_counter.max *.9){
            my_counter.counter.style.color = 'darkorange';
        }else{
            my_counter.counter.style.color = 'green';
        }       
    }
}

my_counters.forEach(sanitize);
