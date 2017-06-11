/**
 * Created by Nabeel on 20-Feb-17.
 */
!function(a,b,c,d){"use strict";b(function(){
// vars
var a=null,c=null,d=null,e=b("#acf-imp-media-item-template").html();
// on browse modal open
b(".gform_wrapper").on("show.bs.modal",".acf-imp-browse-modal",function(){
// trigger load on
var d=b(this).find(".acf-imp-load-more");
// current focused field input & value
a=b("#"+b(this).data("target-input")),c=a.val().split(",").filter(function(a){return a.trim().length>0}),0===d.attr("data-max-id").length&&
// trigger media load on first open
d.trigger("acf-imp-click")}).on("acf-imp-update-value",".acf-imp-browse-modal",function(){
// fetch values
c=b(this).find("input[type=checkbox]:checked").map(function(a,b){return b.value}).toArray(),
// update field
a.val(c.join(","))}).on("change",".acf-imp-media-item input[type=checkbox]",function(a){
// trigger field value update
b(this).closest(".acf-imp-browse-modal").trigger("acf-imp-update-value")}).on("acf-imp-load-media",".acf-imp-browse-modal",function(a){var c=b(this),f=c.find(".acf-imp-load-more");d&&
// terminate previous ongoing request
d.abort(),
// enable loading status
c.trigger("acf-imp-loading"),
// load data
d=b.post(slc_media_picker.ajax_url,{action:"fetch_instagram_media_items",max_id:f.attr("data-max-id")},function(a){if(a.success){
// walk through items list
for(var b=null,d=[],g=0,h=a.data.media_items.length;g<h;g++)b=a.data.media_items[g],
// fill in placeholders
d.push(e.replace(/\{id\}/g,b.media_id).replace("{type}",b.media_type).replace("{thumbnail}",b.image.thumbnail).replace(/\{caption\}/g,b.caption).replace("{likes}",b.counts.likes).replace("{comments}",b.counts.comments));
// append the new items
c.find(".acf-imp-media-items").append(d.join("")),a.data.next_max_id?
// update load more data
f.attr("data-max-id",a.data.next_max_id):
// no more to load after that
f.addClass("hidden")}else
// error loading data
alert(a.data)},"json").always(function(){
// disable loading status
c.trigger("acf-imp-loading-done")})}).on("click acf-imp-click",".acf-imp-load-more",function(){
// load first/more media
b(this).closest(".acf-imp-browse-modal").trigger("acf-imp-load-media")}).on("acf-imp-loading",".acf-imp-browse-modal",function(){this.className+=" is-loading"}).on("acf-imp-loading-done",".acf-imp-browse-modal",function(){this.className=this.className.replace(" is-loading","")})}),Array.prototype.filter||(Array.prototype.filter=function(a){if(void 0===this||null===this)throw new TypeError;var b=Object(this),c=b.length>>>0;if("function"!=typeof a)throw new TypeError;for(var d=[],e=arguments.length>=2?arguments[1]:void 0,f=0;f<c;f++)if(f in b){var g=b[f];
// NOTE: Technically this should Object.defineProperty at
//       the next index, as push can be affected by
//       properties on Object.prototype and Array.prototype.
//       But that method's new, and collisions should be
//       rare, so use the more-compatible alternative.
a.call(e,g,f,b)&&d.push(g)}return d})}(window,jQuery);