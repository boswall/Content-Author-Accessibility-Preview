// if (
//     document.readyState === "complete" ||
//     (document.readyState !== "loading" && !document.documentElement.doScroll)
// ) {
//   caa11ypLoad();
// } else {
//   document.addEventListener("DOMContentLoaded", caa11ypLoad);
// }

// document.attachEvent("onreadystatechange", function(){
//   // check if the DOM is ready
//   if(document.readyState === "complete"){
//
//     document.detachEvent("onreadystatechange", arguments.callee);
//     caa11ypLoad();
//   }
// });
document.addEventListener("DOMContentLoaded", function(){
  caa11ypLoad();
});

document.addEventListener("load", function(){

});

function insertAfter(el, referenceNode) {
    referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
}

var caa11ypLoad = function(){
  caa11ypOptions.tests.forEach(function(test){
    document.querySelectorAll(test.selector).forEach(function(result){
      result.classList.add("caa11yp-error");
      result.classList.add("caa11yp-error-" + test.severity);
      result.classList.add("caa11yp-" + test.id);
      // result.setAttribute('data-caa11yp-label', test.label);
      if (result.nextSibling==null || result.nextSibling.className != 'caa11yp-messages') {
        var messagesWrapper = document.createElement('div');
        var messagesBox = document.createElement('ul');
        messagesWrapper.appendChild(messagesBox);
        messagesWrapper.setAttribute('class', 'caa11yp-messages');
        insertAfter(messagesWrapper, result);
      }
      var messagesBox = result.nextSibling.firstChild;
      messagesBox.innerHTML += '<li class="' + test.severity + '">' + test.label + '</li>';

    });
  });
};
