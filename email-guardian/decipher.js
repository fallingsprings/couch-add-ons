function decipherEmail(key, str, characterSet) {
  let decipheredEmail='';
  let j=0;
  for (let i=0; i < str.length; i++){
    if(characterSet.indexOf(str[i]) < 0 ){
      decipheredEmail += str[i];
    }else{
    j = (characterSet.indexOf(str[i]) - key < 0) ? characterSet.indexOf(str[i]) - key + characterSet.length :  characterSet.indexOf(str[i]) - key;
  decipheredEmail += characterSet[j];
    }
  }
  //reverse direction
  decipheredEmail = decipheredEmail.split("").reverse().join("");
  //decode non-ASCII characters
  return JSON.parse(decipheredEmail);
}
function injectEmail(guardHouse, characterSet){
  for(let cipher of guardHouse){
    let decipheredEmail = decipherEmail(cipher[1], cipher[2], characterSet);
    cipher[0].insertAdjacentHTML('afterend', decipheredEmail);
    cipher[0].parentNode.removeChild(cipher[0]);
  }
}