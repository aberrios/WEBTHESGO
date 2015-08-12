<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:mod_name} - {lang:seed}</td>
  </tr>
  <tr>
    <td class="leftc"><a href="{url:cups_manage}">{lang:back}</a></td>
  </tr>
  <tr>
    <td class="leftc">{seed:error}</td>
  </tr>
{if:no_teams}
  <tr>
    <td class="leftc">{seed:message}</td>
  </tr>
{stop:no_teams}
</table>
{unless:no_teams}
<br />
<form method="post" id="cupsseed" action="{url:cups_seed}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:name}</td>
    <td class="headb">{lang:join}</td>
    <td class="headb">{lang:seed}</td>
    <td class="headb" colspan="2">{lang:options}</td>
  </tr>{loop:teams}
  <tr>
    <td class="leftc">{teams:link}</td>
    <td class="leftc">{teams:join}</td>
    <td class="rightc">{teams:seed_text}</td>
    <td class="leftc">{lang:seed}#<input name="seed_{teams:cupsquads_id}" type="text" maxlength="4" size="4" value="{teams:cupsquads_seed}"></td>
    <td class="leftc"><input name="autoseed_{teams:cupsquads_id}" type="checkbox" value="1" {teams:autoseed_on}> {lang:auto}</td>
  </tr>{stop:teams}
  <tr>
    <td class="leftb" colspan="5"><input type="submit" name="submit" value="{lang:edit}">
			<input type="submit" name="reseed" value="{lang:reseed}">
    	<input type="hidden" name="id" value="{cups:cups_id}"></td>
  </tr>
</table>
</form>
{stop:no_teams}
