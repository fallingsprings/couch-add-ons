function decipherEmail(key, str, characterSet) {
  let newStr='';
  let j=0;
  for (let i=0; i < str.length; i++){
    if(characterSet.indexOf(str[i]) < 0 ){
      newStr += str[i];
    }else{
    j = (characterSet.indexOf(str[i]) - key < 0) ? characterSet.indexOf(str[i]) - key + characterSet.length :  characterSet.indexOf(str[i]) - key;
  newStr += characterSet[j];
    }
  }
  newStr = newStr.split("").reverse().join("");
  return newStr;
}
function injectEmail(guardHouse, characterSet){
  for(let cipher of guardHouse){
    let decipheredLink = decipherEmail(cipher[1], cipher[2], characterSet);
    cipher[0].insertAdjacentHTML('afterend', decipheredLink);
    cipher[0].parentNode.removeChild(cipher[0]);
  }
}
