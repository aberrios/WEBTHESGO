<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb">{lang:mod_name} - {lang:details}</td>
 </tr>
 <tr>
  <td class="leftc">{lang:details_to_cup}</td>
 </tr>
</table>
<br />
{var:message}
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="leftc">{icon:kate} {lang:name}</td>
  <td class="leftb">{cup:cups_name}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:package_games} {lang:game}</td>
  <td class="leftb">{cup:game}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:folder_yellow} {lang:cup_system}</td>
  <td class="leftb">{cup:system}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:folder_yellow} {lang:kind_of_cup}</td>
  <td class="leftb">{cup:kind}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:kdmconfig} {lang:max_participants}</td>
  <td class="leftb">{cup:cups_teams}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:kdmconfig} {lang:registered_participants}</td>
  <td class="leftb">{cup:reg}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:kdmconfig} {lang:checkedin_participants}</td>
  <td class="leftb">{cup:checkedin}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:yast_group_add} {lang:take_part}</td>
  <td class="leftb">{cup:extended}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:1day} {lang:cup_checkin}</td>
  <td class="leftb">{cup:checkin_date}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:1day} {lang:cup_start}</td>
  <td class="leftb">{cup:start_date}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:demo} {lang:status}</td>
  <td class="leftb">{cup:status}{if:running}{cup:status_rounds}{stop:running}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:agt_reload} {lang:rounds_remaining}</td>
  <td class="leftb">{cup:rounds}</td>
 </tr>
 <tr>
  <td class="leftc">{icon:yast_group_add} {lang:winner}</td>
  <td class="leftb">{cup:winner}</td>
 </tr> 
 <tr>
  <td class="leftc">{icon:configure} {lang:extended}</td>
  <td class="leftb"><a href="{cup:match_url}">{lang:to_the_matchlist}</a></td>
 </tr>
 <tr>
  <td class="leftc">{icon:configure} {lang:cupstree}</td>
  <td class="leftb"><a href="{url:cups_tree:id={cup:cups_id}}">{lang:cupstree}</a></td>
 </tr>
 <tr>
  <td class="leftc">{icon:kate} {lang:description}</td>
  <td class="leftb">{cup:cups_text}</td>
 </tr>
</table>

<br />
{if:players}
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb">{lang:players}</td>
  <td class="headb">{lang:checkin}</td>
  <td class="headb">{lang:seed}</td>
 </tr>
 {loop:cup_loop}
 <tr>
  <td class="{if:checked}leftc{stop:checked}{unless:checked}leftb{stop:checked}">{cup_loop:playersflag}&nbsp;{cup_loop:players}</td>
  <td class="{if:checked}rightc{stop:checked}{unless:checked}rightb{stop:checked}">{if:checked}{icon:active}{stop:checked}{unless:checked}{icon:inactive}{stop:checked}</td>
  <td class="{if:checked}rightc{stop:checked}{unless:checked}rightb{stop:checked}">{cup_loop:seed}</td>
 </tr>
 {stop:cup_loop}
</table>
{stop:players}
{if:teams}
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="2">{lang:cup_squads}</td>
  <td class="headb">{lang:checkin}</td>
  <td class="headb">{lang:seed}</td>
 </tr>
 {loop:squads}
 <tr>
  <td class="{if:checked}leftc{stop:checked}{unless:checked}leftb{stop:checked}" valign="top">{squads:name}</td>
  <td class="{if:checked}leftc{stop:checked}{unless:checked}leftb{stop:checked}" valign="top">{loop:members}{members:country} {members:name}{members:dot}<br/>{stop:members}</td>
  <td class="{if:checked}rightc{stop:checked}{unless:checked}rightb{stop:checked}" valign="top">{if:checked}{icon:active}{stop:checked}{unless:checked}{icon:inactive}{stop:checked}</td>
  <td class="{if:checked}rightc{stop:checked}{unless:checked}rightb{stop:checked}" valign="top">{squads:seed}</td>
 </tr>
 {stop:squads}
</table>
{stop:teams}
