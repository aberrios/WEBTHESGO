{if:html}
<link rel="stylesheet" href="{page:path}mods/cups/tree_style.php" type="text/css" />
{stop:html}
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb">{lang:mod_name} {game:icon} {cup:cups_name}</td>
 </tr>
</table>
<br />

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
{if:html}
  {if:brackets}
 <tr>
  <td class="leftc">{lang:grand_final}</td>
 </tr>
 <tr>
  <td class="leftb">
    <br /><br />{grid:extra}<br /><br />
  </td>
 </tr>
  {stop:brackets}
 <tr>
  <td class="leftc">{lang:bracket}</td>
 </tr>
 <tr>
  <td class="leftb">
    <br /><br />{grid:wb}<br /><br />
  </td>
 </tr>
  {if:brackets}
 <tr>
  <td class="leftc">{lang:loser_bracket}</td>
 </tr>
 <tr>
  <td class="leftb">
    <br /><br />{grid:lb}<br /><br />
  </td>
 </tr>
  {stop:brackets}
  {unless:brackets}
  {if:extra}
 <tr>
  <td class="leftc">{lang:third_place}</td>
 </tr>
 <tr>
  <td class="leftb">
    <br /><br />{grid:extra}<br /><br />
  </td>
 </tr>
  {stop:extra}
  {stop:brackets}
{stop:html}
{unless:html}
 <tr>
  <td class="leftb">
    <a href="{page:path}mods/cups/tree.php?id={cups:id}&width={options:width_lightbox}" rel="lightbox-cupsid-{cups:id}" title="{cup:cups_name}{if:brackets} (WB){stop:brackets}"><img src="{page:path}mods/cups/tree.php?id={cups:id}" alt="tree" /></a>{if:brackets}<br />
    <a href="{page:path}mods/cups/tree_losers.php?id={cups:id}&width={options:width_lightbox}" rel="lightbox-cupsid-{cups:id}" title="{cup:cups_name} (LB)"><img src="{page:path}mods/cups/tree_losers.php?id={cups:id}" alt="tree" /></a>{stop:brackets}
  </td>
 </tr>
{stop:html}
</table>
