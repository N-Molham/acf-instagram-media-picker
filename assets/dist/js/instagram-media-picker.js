/**
 * Created by Nabeel on 20-Feb-17.
 */
!function(a,b,c,d){"use strict";function e(a,b){var c,d=[{value:1e18,symbol:"E"},{value:1e15,symbol:"P"},{value:1e12,symbol:"T"},{value:1e9,symbol:"G"},{value:1e6,symbol:"M"},{value:1e3,symbol:"k"}],e=/\.0+$|(\.[0-9]*[1-9])0+$/;for(c=0;c<d.length;c++)if(a>=d[c].value)return(a/d[c].value).toFixed(b).replace(e,"$1")+d[c].symbol;return a.toFixed(b).replace(e,"$1")}b(function(){
// vars
var a=null,c=null,f=null,g=null,h=null,i=!1,j=b("#acf-imp-media-item-template").html();
// on browse modal open
b(".acf-fields").on("show.bs.modal",".acf-imp-browse-modal",function(d){
// trigger load on
var e=b(this),h=e.find(".acf-imp-load-more");
// current focused field input & value
c=b(d.relatedTarget),a=b("#"+e.data("value-input")),g=b("#"+e.data("username-input")),f=a.val().split(",").filter(function(a){return a.trim().length>0}),e.find(".acf-imp-username").val(g.val()),0===h.attr("data-max-id").length&&
// trigger media load on first open
h.trigger("acf-imp-click",[e])}).on("acf-imp-update-value",".acf-imp-browse-modal",function(){var d=b(this),e=d.find("input[type=checkbox]:checked");e.length>d.data("media-limit")?
// limit reached
e.each(function(a,b){f.indexOf(b.value)<0&&(
// remove selection from over limit
b.checked=!1)}):(
// fetch values
f=e.map(function(a,b){return b.value}).toArray(),
// update field
a.val(f.join(",")),f.length&&
// set selected media count
c.text(c.data("browse-label")+" ("+f.length.toString()+")")),d.find(".acf-imp-media-item").removeClass("active"),d.find("input[type=checkbox]:checked").closest(".acf-imp-media-item").addClass("active")}).on("change",".acf-imp-media-item input[type=checkbox]",function(a){
// trigger field value update
b(this).closest(".acf-imp-browse-modal").trigger("acf-imp-update-value")}).on("acf-imp-load-media",".acf-imp-browse-modal",function(){var a=b(this),c=a.find(".acf-imp-username").val().trim().replace(/[^a-zA-Z0-9\._]/g,""),d=a.find(".acf-imp-load-more");if(c.length<3)
// invalid username!
return!0;h&&
// terminate previous ongoing request
h.abort(),
// enable loading status
a.trigger("acf-imp-loading"),i=!0;var g=a.find(".acf-imp-media-items").addClass("hide");
// load data
h=b.post(acf_imp_media_picker.ajax_url,{action:"fetch_instagram_media_items",nonce:acf_imp_media_picker.ajax_nonce,username:c,field_id:a.closest(".acf-field").data("key")},function(a){if(a.success){
// walk through items list
for(var b=null,c=[],h=0,i=a.data.length;h<i;h++)b=a.data[h],
// if item is selected or not
b.selected=f.indexOf(b.code)>-1,
// fill in placeholders
c.push(j.replace(/\{code\}/g,b.code).replace("{type}",b.type).replace("{thumbnail}",b.image.thumbnail).replace("{likes}",e(b.counts.likes,0)).replace("{comments}",e(b.counts.comments,0)).replace("{active}",b.selected?"active":"").replace("{checked}",b.selected?'checked="checked"':""));
// append the new items
g.html(c.join("")),
// no more to load after that
d.addClass("hidden")}else
// error loading data
alert(a.data)},"json").always(function(){
// disable loading status
a.trigger("acf-imp-loading-done"),i=!1,g.removeClass("hide")})}).on("click acf-imp-click",".acf-imp-load-more",function(a,c){d===c&&(c=b(this).closest(".acf-imp-browse-modal")),
// load first/more media
c.trigger("acf-imp-load-media")}).on("keydown keyup",".acf-imp-username",function(a){var c=b(this);"keydown"===a.type&&13===a.keyCode?(
// prevent from submitting the form
a.preventDefault(),!1===i&&
// run load code
c.siblings(".acf-imp-load-more").trigger("click")):
// bind value
b("#"+c.data("username-input")).val(c.val())}).on("acf-imp-loading",".acf-imp-browse-modal",function(){this.className+=" is-loading"}).on("acf-imp-loading-done",".acf-imp-browse-modal",function(){this.className=this.className.replace(" is-loading","")})}),Array.prototype.filter||(Array.prototype.filter=function(a){if(void 0===this||null===this)throw new TypeError;var b=Object(this),c=b.length>>>0;if("function"!=typeof a)throw new TypeError;for(var d=[],e=arguments.length>=2?arguments[1]:void 0,f=0;f<c;f++)if(f in b){var g=b[f];
// NOTE: Technically this should Object.defineProperty at
//       the next index, as push can be affected by
//       properties on Object.prototype and Array.prototype.
//       But that method's new, and collisions should be
//       rare, so use the more-compatible alternative.
a.call(e,g,f,b)&&d.push(g)}return d})}(window,jQuery);