import Vue from "vue";

//// vue-toasted
window.showSuccessMessage = function (message, duration = 4500) {
  Vue.toasted.success(message, {
    duration: duration,
    icon: {name: 'mdi-check-bold'}
  });
}

window.showErrorMessage = function (message, duration = 4500) {
  Vue.toasted.error(message, {
    duration: duration,
    icon: {name: 'mdi-close-circle  '}
  });
}

window.showInfoMessage = function (message, duration = 4500) {
  Vue.toasted.info(message, {
    duration: duration,
    icon: {name: 'mdi-information'}
  });
}

window.showMessage = function (message, duration = 4500) {
  Vue.toasted.show(message, {
    duration: duration,
    icon: {name: 'mdi-comment-processing'}
  });
}

window.showWarningMessage = function (message, duration = 4500) {
  Vue.toasted.show(message, {
    duration: duration,
    className: 'warning-toast',
    icon: {name: 'mdi-alert'}
  });
}

// prints formData values
window.printFormData = function (formData) {
  console.log("{");
  for (let pair of formData.entries()) {
    console.log("  ", pair[0] + ': ' + pair[1]);
  }
  console.log("}");
}

// builds formData from a json formatted obj
window.buildFormData = function (obj) {
  let formData = new FormData();
  for (let index in obj) {
    formData.append(index, obj[index]);
    console.log("build for", index);
    console.log("build for", obj[index]);
  }
  return formData;
}

window.pluck = function (arr, key) {
  let elems = [];
  for (let index in arr) {
    elems.push(arr[index][key]);
  }
  return elems;
}

// App\Parameter {#4112
//   id: 1,
//     name: "exchange_rate",
//     text: "Tipo de cambio",
//     value: "6.96",
//     created_at: "2022-02-22 16:48:44",
//     updated_at: "2022-02-22 16:48:44",
// }
// given an array  of  parameter objects like the above, obtain it's value

window.getParamVal = function (arrObj, key) {
  for (let index in arrObj) {
    if (arrObj[index]["name"] === key) {
      return arrObj[index]["value"];
    }
  }
  return null;
}

//// frontend details validation
// validate emptiness
function isAllEmpty(obj) {
  let count = 0;
  for (let i in obj) {
    if (("" + obj[i]).trim() != "") {
      count++;
    }
  }
  return count === 0; // with 0 records all is empty
}

// validate if all keys have value
function hasAllRequired(obj, arrKeys) {
  let coincidences = 0;
  for (let i in arrKeys) {
    if (obj[arrKeys[i]]) {
      coincidences++;
    }
  }
  console.log("concidences", coincidences);
  console.log("arrKeys.length", arrKeys.length);
  return coincidences === arrKeys.length; // true if all exists, false otherwise
}

// returns rows indexes starting at 1 for all invalid rows
window.getInvalidRowsIndexes = function (arrObj, mustExistKeys) {
  let invalidRows = [];
  for (let i in arrObj) {
    if (!hasAllRequired(arrObj[i], mustExistKeys)){
      invalidRows.push( parseInt(i)+1) ;
    }
  }
  console.log("invalid rows", invalidRows);
  return invalidRows;
}

// returns an array with all rows cleaned
window.cleanEmptyRows = function (arrObj) {
  let arrRes = [];
  for (let i in arrObj) {
    if (isAllEmpty(arrObj[i]) === false) {
      arrRes.push(arrObj[i]);
    }
  }
  return arrRes;
}
