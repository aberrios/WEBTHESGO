<form method="post" action="{url:cups_teamadd}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb" colspan="2">{lang:mod_name} - {lang:create}</td>
  </tr>
  <tr>
    <td class="headb" colspan="2">{cup:message}</td>
  </tr>
  {if:user}
  <tr>
    <td class="leftc">{lang:search}</td>
    <td class="leftb">
			<input type="text" name="team_name" id="team_name" value="{team:team_name}" autocomplete="off" onkeyup="Clansphere.ajax.user_autocomplete('team_name', 'team_name_result', '{page:path}')" size="50" maxlength="100" />
      <div id="team_name_result"></div>
    </td>
  </tr>
  {stop:user}
  {unless:user}
  <tr>
    <td class="leftc">{lang:team}</td>
    <td class="leftb">{teams:select}</td>
  </tr>
  {stop:user}
  <tr>
    <td class="leftc">{icon:ksysguard} {lang:options}</td>
    <td class="leftb">
      <input type="hidden" name="id" value="{team:cups_id}" />
      <input type="submit" name="submit" value="{lang:create}" /></td>
  </tr>
</table>
</form>
