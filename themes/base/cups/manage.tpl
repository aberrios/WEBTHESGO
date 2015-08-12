<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:mod_name} - {lang:manage}</td>
  </tr>
  <tr>    
    <td class="leftb">{icon:contents} {lang:total}: {vars:count}</td>
  </tr>
</table>
<br />
{vars:message}

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:game}</td>
    <td class="headb">{lang:name}</td>
    <td class="headb">{lang:cup_system}</td>
    <td class="headb">{lang:teams}</td>
    <td class="headb" colspan="5">{lang:options}</td>
  </tr>{loop:cups}
  <tr>
    <td class="leftc">{cups:game}</td>
    <td class="leftc"><a href="{url:cups_view:id={cups:cups_id}}">{cups:cups_name}</a></td>
    <td class="leftc">{cups:cups_system}</td>
    <td class="rightc">{cups:participations} / {cups:checkedin} / {cups:cups_teams}</td>
    <td class="leftc">{if:start}{cups:start_link}{stop:start}{unless:start}{if:restart}{cups:restart_link}{stop:restart}{unless:restart}-{stop:restart}{stop:start}</td>
    <td class="leftc"><a href="{url:cups_teams:where={cups:cups_id}}" title="{lang:teams}">{icon:kdmconfig}</a></td>
    <td class="leftc">{if:seed}<a href="{url:cups_seed:id={cups:cups_id}}" title="{lang:seed}">{icon:enumList}</a>{stop:seed}{unless:seed}-{stop:seed}</td>
    <td class="leftc"><a href="{url:cups_edit:id={cups:cups_id}}" title="{lang:edit}">{icon:edit}</a></td>
    <td class="leftc"><a href="{url:cups_remove:id={cups:cups_id}}" title="{lang:remove}">{icon:editdelete}</a></td>
  </tr>{stop:cups}
</table>
