/**
 * Created by Nabeel on 20-Feb-17.
 */
!function(a,b,c,d){"use strict";b(function(){
// vars
var a=null,c=null,e=null,f=null,g=null,h=!1,i=b("#acf-imp-media-item-template").html();
// on browse modal open
b(".acf-fields").on("show.bs.modal",".acf-imp-browse-modal",function(d){
// trigger load on
var g=b(this),h=g.find(".acf-imp-load-more");
// current focused field input & value
c=b(d.relatedTarget),a=b("#"+g.data("value-input")),f=b("#"+g.data("username-input")),e=a.val().split(",").filter(function(a){return a.trim().length>0}),g.find(".acf-imp-username").val(f.val()),0===h.attr("data-max-id").length&&
// trigger media load on first open
h.trigger("acf-imp-click",[g])}).on("acf-imp-update-value",".acf-imp-browse-modal",function(){var d=b(this).find("input[type=checkbox]:checked");d.length>b(this).data("media-limit")?
// limit reached
d.each(function(a,b){e.indexOf(b.value)<0&&(
// remove selection from over limit
b.checked=!1)}):(
// fetch values
e=d.map(function(a,b){return b.value}).toArray(),
// update field
a.val(e.join(",")),e.length&&
// set selected media count
c.text(c.data("browse-label")+" ("+e.length.toString()+")"))}).on("change",".acf-imp-media-item input[type=checkbox]",function(a){
// trigger field value update
b(this).closest(".acf-imp-browse-modal").trigger("acf-imp-update-value")}).on("acf-imp-load-media",".acf-imp-browse-modal",function(){var a=b(this),c=a.find(".acf-imp-username").val().trim().replace(/[^a-zA-Z0-9\._]/g,""),d=a.find(".acf-imp-load-more");if(c.length<3)
// invalid username!
return!0;g&&
// terminate previous ongoing request
g.abort(),
// enable loading status
a.trigger("acf-imp-loading"),h=!0;var f=a.find(".acf-imp-media-items").addClass("hide");
// load data
g=b.post(acf_imp_media_picker.ajax_url,{action:"fetch_instagram_media_items",nonce:acf_imp_media_picker.ajax_nonce,username:c},function(a){if(a.success){
// walk through items list
for(var b=null,c=[],g=0,h=a.data.length;g<h;g++)b=a.data[g],
// fill in placeholders
c.push(i.replace(/\{code\}/g,b.code).replace("{type}",b.type).replace("{thumbnail}",b.image.thumbnail).replace("{likes}",b.counts.likes).replace("{comments}",b.counts.comments).replace("{checked}",e.indexOf(b.code)>-1?'checked="checked"':""));
// append the new items
f.html(c.join("")),
// no more to load after that
d.addClass("hidden")}else
// error loading data
alert(a.data)},"json").always(function(){
// disable loading status
a.trigger("acf-imp-loading-done"),h=!1,f.removeClass("hide")})}).on("click acf-imp-click",".acf-imp-load-more",function(a,c){d===c&&(c=b(this).closest(".acf-imp-browse-modal")),
// load first/more media
c.trigger("acf-imp-load-media")}).on("keydown keyup",".acf-imp-username",function(a){var c=b(this);"keydown"===a.type&&13===a.keyCode?(
// prevent from submitting the form
a.preventDefault(),!1===h&&
// run load code
c.siblings(".acf-imp-load-more").trigger("click")):
// bind value
b("#"+c.data("value-input")).val(c.val())}).on("acf-imp-loading",".acf-imp-browse-modal",function(){this.className+=" is-loading"}).on("acf-imp-loading-done",".acf-imp-browse-modal",function(){this.className=this.className.replace(" is-loading","")})}),Array.prototype.filter||(Array.prototype.filter=function(a){if(void 0===this||null===this)throw new TypeError;var b=Object(this),c=b.length>>>0;if("function"!=typeof a)throw new TypeError;for(var d=[],e=arguments.length>=2?arguments[1]:void 0,f=0;f<c;f++)if(f in b){var g=b[f];
// NOTE: Technically this should Object.defineProperty at
//       the next index, as push can be affected by
//       properties on Object.prototype and Array.prototype.
//       But that method's new, and collisions should be
//       rare, so use the more-compatible alternative.
a.call(e,g,f,b)&&d.push(g)}return d})}(window,jQuery);