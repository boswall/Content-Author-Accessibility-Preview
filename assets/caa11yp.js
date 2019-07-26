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

if (typeof wp.data !== 'undefined') {
  const getBlockList = () => wp.data.select( 'core/editor' ).getBlocks();
  let blockList = getBlockList();
  wp.data.subscribe(() => {
    const newBlockList = getBlockList();
    const blockListChanged = newBlockList !== blockList;
    blockList = newBlockList;
    if ( blockListChanged ) {
      // You can trigger here any behavior when the block list in the post changes.
      caa11ypClear();
      caa11ypLoad();
    }
  });
}

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
        messagesBox.setAttribute('class', 'caa11yp-box');
        messagesWrapper.appendChild(messagesBox);
        messagesWrapper.setAttribute('class', 'caa11yp-messages');
        insertAfter(messagesWrapper, result);
      }
      var messagesBox = result.nextSibling.firstChild;
      messagesBox.innerHTML += '<li class="' + test.severity + '">' + test.label + '</li>';

    });
  });
};

var caa11ypClear = function(){
    document.querySelectorAll(".caa11yp-error").forEach(function(result){
      result.classList.forEach(function(className){
        if (className.startsWith('caa11yp-')) {
          result.classList.remove(className);
        }
      });

    });
    document.querySelectorAll(".caa11yp-messages").forEach(function(el){
      el.parentNode.removeChild(el);
    });
    document.querySelectorAll(".caa11yp-box").forEach(function(el){
      el.parentNode.removeChild(el);
    });
};
