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
