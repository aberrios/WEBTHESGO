<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:mod_name} - {lang:create}</td>
  </tr>
  <tr>
    <td class="leftc">{lang:edit_cup}</td>
  </tr>
</table>
<br />

<form method="post" id="cupscreate" action="{url:cups_edit}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="leftc">{icon:kate} {lang:name} *</td>
    <td class="leftb"><input type="text" name="cups_name" value="{cups:cups_name}" maxlength="100" size="35" /></td>
  </tr>
  <tr>
    <td class="leftc">{icon:package_games} {lang:game} *</td>
    <td class="leftb">
      <select name="games_id" onchange="cs_gamechoose(this.form)">
        <option value="0" selected="selected">----</option>{loop:games}
        <option value="{games:games_id}"{games:selected}>{games:games_name}</option>{stop:games}
    </select> <img src="{page:path}uploads/games/0.gif" id="game_1" alt="" /></td>
  </tr>
  <tr>
    <td class="leftc">{icon:folder_yellow} {lang:cup_system}</td>
    <td class="leftb">
     <select name="cups_system">
      <option value="teams"{sel:teams}>{lang:teams}</option>
      <option value="users"{sel:users}>{lang:users}</option>
     </select></td>
  </tr>
  <tr>
    <td class="leftc">{icon:folder_yellow} {lang:kind_of_cup}</td>
    <td class="leftb">
     <select name="cups_brackets">
      <option value="0"{sel:ko}>{lang:ko}</option>
      <option value="2"{sel:ko3rd}>{lang:ko3rd}</option>
      <option value="1"{sel:brackets}>{lang:brackets}</option>
     </select></td>
  </tr>
  <tr>
    <td class="leftc">{icon:kdmconfig} {lang:max_teams} *</td>
    <td class="leftb"><input type="text" name="cups_teams" value="{cups:teams}" maxlength="6" size="6" /></td>
  </tr>
  <tr>
    <td class="leftc">{icon:kate} {lang:text}</td>
    <td class="leftb"><textarea class="rte_abcode" name="cups_text" cols="40" rows="20" id="cups_text">{cups:cups_text}</textarea></td>
  </tr>
  <tr>
    <td class="leftc">{icon:1day} {lang:cup_checkin}</td>
    <td class="leftb">{cups:checkin}</td>
  </tr>
  <tr>
    <td class="leftc">{icon:1day} {lang:cup_start}</td>
    <td class="leftb">{cups:start}</td>
  </tr>
  <tr>
    <td class="leftc">{icon:1day} {lang:notify_hours}</td>
    <td class="leftb"><input type="text" name="cups_notify_hours" value="{cups:cups_notify_hours}" maxlength="2" size="2" /></td>
  </tr>
  <tr>
    <td class="leftc">{icon:1day} {lang:notify_via_pm}</td>
    <td class="leftb"><input type="checkbox" name="cups_notify_pm" value="1" {cups:cups_notify_pm_checked} /></td>
  </tr>
  <tr>
    <td class="leftc">{icon:1day} {lang:notify_via_email}</td>
    <td class="leftb"><input type="checkbox" name="cups_notify_email" value="1" {cups:cups_notify_email_checked} /></td>
  </tr>
  <tr>
    <td class="leftc">{icon:1day} {lang:notified}</td>
    <td class="leftb"><input type="checkbox" name="cups_notified" value="1" {cups:cups_notified_checked} /></td>
  </tr>
	<tr>
		<td class="leftc">{icon:access} {lang:needed_access}</td>
		<td class="leftb" colspan="2">
			<select name="cups_access">
				{loop:access}
				{access:sel}
				{stop:access}
			</select>
		</td>
	</tr>
  <tr>
    <td class="leftc">{icon:ksysguard} {lang:options}</td>
    <td class="leftb">
      <input type="submit" name="submit" value="{lang:edit}" />
            <input type="hidden" name="cups_id" value="{cups:cups_id}" />
     </td>
  </tr>
</table>
</form>
