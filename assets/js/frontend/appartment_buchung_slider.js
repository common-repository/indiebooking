jQuery(document).ready(function(o){var e={$FillMode:1,$AutoPlay:!0,$SlideDuration:500,$ArrowKeyNavigation:!0,$ThumbnailNavigatorOptions:{$Class:$JssorThumbnailNavigator$,$ChanceToShow:2,$ActionMode:1,$SpacingX:8,$DisplayPieces:10,$ParkingPosition:360},$ArrowNavigatorOptions:{$Class:$JssorArrowNavigator$,$ChanceToShow:2,$AutoCenter:2,$Steps:1,$Scale:!1},$BulletNavigatorOptions:{$Class:$JssorBulletNavigator$,$ChanceToShow:2,$AutoCenter:1,$Steps:1,$Rows:1,$SpacingX:12,$SpacingY:4,$Orientation:1,$Scale:!1}};jQuery(".slider1_container").each(function(o,i){function n(){var o=t.$Elmt.parentNode.clientWidth;console.log(t.$Elmt.parentNode),o?(o=Math.min(o,880),console.log(o),t.$ScaleWidth(o)):window.setTimeout(n,30)}var a=jQuery(i).attr("id"),t=new $JssorSliderjQuery(a,e);console.log(t),n(),jQuery(window).bind("load",n),jQuery(window).bind("resize",n),jQuery(window).bind("orientationchange",n)})});