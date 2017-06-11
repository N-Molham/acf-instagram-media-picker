/**
 * Created by Nabeel on 20-Feb-17.
 */
!function(a,b,c,d){"use strict";b(function(){
// vars
var a=null,c=null,e=null,f=b("#acf-imp-media-item-template").html();
// on browse modal open
b(".acf-fields").on("show.bs.modal",".acf-imp-browse-modal",function(){
// trigger load on
var d=b(this),e=d.find(".acf-imp-load-more");
// current focused field input & value
a=b("#"+d.data("target-input")),c=a.val().split(",").filter(function(a){return a.trim().length>0}),0===e.attr("data-max-id").length&&
// trigger media load on first open
e.trigger("acf-imp-click",[d])}).on("acf-imp-update-value",".acf-imp-browse-modal",function(){
// fetch values
c=b(this).find("input[type=checkbox]:checked").map(function(a,b){return b.value}).toArray(),
// update field
a.val(c.join(","))}).on("change",".acf-imp-media-item input[type=checkbox]",function(a){
// trigger field value update
b(this).closest(".acf-imp-browse-modal").trigger("acf-imp-update-value")}).on("acf-imp-load-media",".acf-imp-browse-modal",function(){var a=b(this),c=a.find(".acf-imp-username").val().trim().replace(/[^a-zA-Z0-9\._]/g,""),d=a.find(".acf-imp-load-more");
// terminate previous ongoing request
// enable loading status
// load data
return c.length<3||(e&&e.abort(),a.trigger("acf-imp-loading"),void(e=b.post(acf_imp_media_picker.ajax_url,{action:"fetch_instagram_media_items",username:c,max_id:d.attr("data-max-id")},function(b){if(b.success){
// walk through items list
for(var c=null,e=[],g=0,h=b.data.media_items.length;g<h;g++)c=b.data.media_items[g],
// fill in placeholders
e.push(f.replace(/\{id\}/g,c.media_id).replace("{type}",c.media_type).replace("{thumbnail}",c.image.thumbnail).replace(/\{caption\}/g,c.caption).replace("{likes}",c.counts.likes).replace("{comments}",c.counts.comments));
// append the new items
a.find(".acf-imp-media-items").append(e.join("")),b.data.next_max_id?
// update load more data
d.attr("data-max-id",b.data.next_max_id):
// no more to load after that
d.addClass("hidden")}else
// error loading data
alert(b.data)},"json").always(function(){
// disable loading status
a.trigger("acf-imp-loading-done")})))}).on("click acf-imp-click",".acf-imp-load-more",function(a,c){d===c&&(c=b(this).closest(".acf-imp-browse-modal")),
// load first/more media
c.trigger("acf-imp-load-media")}).on("keydown",".acf-imp-username",function(a){13===a.keyCode&&(
// prevent from submitting the form
a.preventDefault(),
// run load code
b(this).siblings(".acf-imp-load-more").trigger("click"))}).on("acf-imp-loading",".acf-imp-browse-modal",function(){this.className+=" is-loading"}).on("acf-imp-loading-done",".acf-imp-browse-modal",function(){this.className=this.className.replace(" is-loading","")})}),Array.prototype.filter||(Array.prototype.filter=function(a){if(void 0===this||null===this)throw new TypeError;var b=Object(this),c=b.length>>>0;if("function"!=typeof a)throw new TypeError;for(var d=[],e=arguments.length>=2?arguments[1]:void 0,f=0;f<c;f++)if(f in b){var g=b[f];
// NOTE: Technically this should Object.defineProperty at
//       the next index, as push can be affected by
//       properties on Object.prototype and Array.prototype.
//       But that method's new, and collisions should be
//       rare, so use the more-compatible alternative.
a.call(e,g,f,b)&&d.push(g)}return d})}(window,jQuery);