//function localizeCounter() and 
//array characterCounters
//are added by server-side PHP

function sanitizeCounter(characterCounter) {
    characterCounter.field = (!characterCounter.field) ? '' : characterCounter.field.trim();
    characterCounter.repeatable = (!characterCounter.repeatable) ? '' : characterCounter.repeatable.trim();
    characterCounter.editable = (!characterCounter.editable) ? '' : characterCounter.editable.trim();
    characterCounter.max = (!characterCounter.max) ? '' : parseInt(characterCounter.max);
    characterCounter.min = (!characterCounter.min) ? '' : parseInt(characterCounter.min);
    characterCounter.type = (!characterCounter.type) ? '' : characterCounter.type.trim().toLowerCase();
    characterCounter.show = (!characterCounter.show) ? '' : characterCounter.show.trim().toLowerCase();
    characterCounter.label = (!characterCounter.label) ? '' : characterCounter.label;
    characterCounter.enforce_max = (!characterCounter.enforce_max) ? false : true;
    characterCounter.count = 0;
    //can't count down from nothing
    if (characterCounter.max === '') { characterCounter.type = 'up'; }
    
    initCounter(characterCounter);
}
function initCounter(characterCounter) {
    if (!characterCounter.repeatable) {
        //regular static fields
        //instantiate counter for editable field or front-end field    
        let field_name = (characterCounter.field) || `f_${characterCounter.editable}`;
        characterCounter.target = document.getElementById(field_name);
        instantiateCounter(characterCounter);
    }else{
        //dynamic repeatable regions
        //identify all repeatable fields
        let repeatable = document.getElementById(`f_${characterCounter.repeatable}`); 
        let my_selector = `.k_element_${characterCounter.editable} input`;
        let targets = repeatable.querySelectorAll(my_selector);

        //edge case for initially empty repeatable tables        
        if(!targets.length){
           //mutation observer instantiates new repeatable field when it is added dynamically
            const repeatable_observer = new MutationObserver(function() {
            let targets = repeatable.querySelectorAll(my_selector);
            let target = targets.item(0);
                initRepeatable(characterCounter, target);
                repeatable_observer.disconnect();
            });
            repeatable_observer.observe(repeatable, {subtree: true, childList: true});
           }
        
        //instantiate counters
        for (let target of targets){
            initRepeatable(characterCounter, target);
        }
        
        //listener to add counter when a new row is added
        let add_row_button = document.getElementById(`addRow_f_${characterCounter.repeatable}`);
        add_row_button.addEventListener('click', function () {
            //identify the new row and instantiate counter
            let targets = repeatable.querySelectorAll(my_selector);
            let target = targets.item(targets.length - 1);
            initRepeatable(characterCounter, target);
        });        
    }
}

function initRepeatable(characterCounter, target){
    let repeatable_counter = {
        max: characterCounter.max,
        min: characterCounter.min,
        type: characterCounter.type,
        show: characterCounter.show,
        label: characterCounter.label,
        enforce_max: characterCounter.enforce_max,
        count: 0,
        target: target
    };
    instantiateCounter(repeatable_counter);
}

function instantiateCounter(characterCounter) {
    const LANG = localizeCounter();
    
    //Add keyboard listener to target field
    characterCounter.target.addEventListener('keyup', function () {
        refreshCount(characterCounter);
    });
    characterCounter.target.addEventListener('keydown', function () {
        refreshCount(characterCounter);
    });
            
    //create new counter element
    characterCounter.id = `${characterCounter.target.id}_counter`;
    let new_counter = document.createElement('p');
    new_counter.setAttribute('id', characterCounter.id);
    new_counter.style.textAlign = 'right';
    characterCounter.target.parentNode.appendChild(new_counter);
        
    //create dynamic span for count
    characterCounter.element = document.getElementById(characterCounter.id);
    characterCounter.element.innerHTML = `<span>${characterCounter.count}</span>`;
    
    //create static text
    //show min
    if (characterCounter.min && (characterCounter.show.indexOf('min') >= 0 || characterCounter.show.indexOf('both') >= 0)) {
        characterCounter.element.innerHTML = `${LANG.min} ${characterCounter.min} &nbsp; ${characterCounter.element.innerHTML}`;
            }
    //show max
    if (characterCounter.max && (characterCounter.show.indexOf('max') >= 0 || characterCounter.show.indexOf('both') >= 0)) {
        characterCounter.element.innerHTML = `${characterCounter.element.innerHTML}&nbsp;&nbsp;&nbsp;${LANG.max} ${characterCounter.max}`;
    }
    //show label
    characterCounter.element.innerHTML = `${characterCounter.label} ${characterCounter.element.innerHTML}`;
    
    //create dynamic span for warning
    if(characterCounter.enforce_max){
        characterCounter.element.innerHTML = `<span style="color:red;">${LANG.warning}</span> ${characterCounter.element.innerHTML}`;
    }
    
    //identify counter span and warning span
    characterCounter.warning = (!characterCounter.enforce_max) ? null : characterCounter.element.children[0];
    characterCounter.counter = (characterCounter.enforce_max) ? characterCounter.element.children[1] : characterCounter.element.children[0];
    
    refreshCount(characterCounter);  
}

function refreshCount(characterCounter) {
    //enforce maximum character count
    if (characterCounter.enforce_max){
        if( characterCounter.target.value.length >= characterCounter.max){
            characterCounter.target.value = characterCounter.target.value.slice(0, characterCounter.max);
            //show/hide warning
            characterCounter.warning.style.visibility = 'visible';
        }else{
            characterCounter.warning.style.visibility = 'hidden';
        }
    }
    
    //calculate count
    if (characterCounter.type === 'up'){
        characterCounter.count = characterCounter.target.value.length; 
    }else{
        characterCounter.count = characterCounter.max - characterCounter.target.value.length;
    }
    characterCounter.counter.innerHTML = characterCounter.count;
    
    //Counter styles
    if (characterCounter.type === 'up'){
        //count up
        if ((characterCounter.max && characterCounter.count > characterCounter.max) || characterCounter.count < characterCounter.min){
            characterCounter.counter.style.color = 'red';
        }else if (characterCounter.max && characterCounter.count > characterCounter.max * .9){
            characterCounter.counter.style.color = 'darkorange';
        }else{
            characterCounter.counter.style.color = 'green';
        }
    }else{ 
        //count down
        if (characterCounter.min && characterCounter.target.value.length < characterCounter.min || characterCounter.count < 0) {
            characterCounter.counter.style.color = 'red';
        }else if(characterCounter.target.value.length > characterCounter.max *.9){
            characterCounter.counter.style.color = 'darkorange';
        }else{
            characterCounter.counter.style.color = 'green';
        }       
    }
}

characterCounters.forEach(sanitizeCounter);
