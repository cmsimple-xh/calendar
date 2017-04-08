CMSimple Calendar 1.4 Event-List Template

===<!-- 1 - headline annoncing the time period shown-->

<p>%period% %start% %till% %end%</p>

===<!-- 2 - headline weekly section-->

<h4 style='text-align:center;margin:0;padding:0;position:relative;top:1em;color:#b66;'>%weekly%</h4>

===<!-- 3 - weekday header-->

<p style='margin:3em 0 .2em;'><big><i>%longweekday%</i></big></p>

===<!-- 4 - weekly events-->

<div style='border:2px solid #bb8;padding:2px 6px;margin:0 0 15px 0;background:#fafaef;'>
<div class="bookedout">%bookedout%</div>
<h4 style='text-align:center;margin-bottom:0;'>%mainfield%</h4>
<p style='text-align:center;margin-bottom:0;color:#660'><b>%field3%</b></p>
<p><b>%shortweekday% %time%</b><br>%date% %field1% %infolink%</p>
<div>%description%</div>
<div style='clear:both;'></div>
</div>

===<!-- 5 - past weekly events-->

<div style='border:1px dashed #bbb;padding:2px 6px;margin:0 0 15px 0;background:#fafafa;'>
<p style="position:absolute;font-size:150%; fontweight:bold; color:#daa;">%ended%</p>
<p style='text-align:center;color:#aaa;margin:0;letter-spacing:0.1em;' class="past_event">%mainfield%</p>
<p style='text-align:center;color:#aaa;margin:0;' class="past_event">%field3%</p>
<p style='color:#aaa;;margin:0;' class="past_event"><b>%shortweekday% %time%</b> %date% %field1% %infolink%</p>
</div>

===<!-- 6 - headline single events section-->

<h4 style='text-align:center;margin:1.5em 0 0;padding:0;position:relative;top:.5em;color:#b66;'>%single%</h4>

===<!-- 7 - month header-->

<p><br><br><big><i>%month% %year%</i></big></p>

===<!-- 8 - yearly events with age calculation -->

<div style='border:2px solid #8b8;padding:2px 6px;margin:0 0 15px 0;background:#effaef;'>
<div class="bookedout">%bookedout%</div>
<h4 style='text-align:center;margin-bottom:0;'>%mainfield%</h4>
<h5 style='text-align:center;margin-bottom:0;'>%age%</h5>
<p style='text-align:center;margin-bottom:0;color:#660'><b>%field3%</b></p>
<p><b>%date%</b><br> %time% %field1% %infolink%</p>
<div>%description%</div>
<div style='clear:both;'></div>
</div>

===<!-- 9 - yearly events -->

<div style='border:2px solid #bb8;padding:2px 6px;margin:0 0 15px 0;background:#fafaef;'>
<div class="bookedout">%bookedout%</div>
<h4 style='text-align:center;margin-bottom:0;'>%mainfield%</h4>
<p style='text-align:center;margin-bottom:0;color:#660' class="past_event"><b>%field3%</b></p>
<p><b>%date%</b><br> %time% %field1% %infolink%</p>
<div>%description%</div>
<div style='clear:both;'></div>
</div>

===<!-- 10 - past yearly events-->

<div style='border:1px dashed #bbb;padding:2px 6px;margin:0 0 15px 0;background:#fafafa;'>
<p style="position:absolute;font-size:150%; fontweight:bold; color:#daa;">%ended%</p>
<p style='text-align:center;color:#aaa;margin:0;letter-spacing:0.1em;' class="past_event">%mainfield%</p>
<p style='text-align:center;color:#aaa;margin:0;' class="past_event"><b>%field3% %age%</b></p>
<p style='color:#aaa;;margin:0;' class="past_event"><b>%date%</b> %time% %field1% %infolink%</p>
</div>

===<!-- 11 - single events-->

<div style='border:2px solid #bb8;padding:2px 6px;margin:0 0 15px 0;background:#fafaef;'>
<div class="bookedout">%bookedout%</div>
<h4 style='text-align:center;margin-bottom:0;'>%mainfield%</h4>
<p style='text-align:center;margin-bottom:0;color:#660'><b>%field3%</b></p>
<p><b>%date%</b><br> %time% %field1% %infolink%</p>
<div>%description%</div>
<div style='clear:both;'></div>
</div>

===<!-- 12 - past single events-->

<div style='border:1px dashed #bbb;padding:2px 6px;margin:0 0 15px 0;background:#fafafa;'>
<p style="position:absolute;font-size:150%; fontweight:bold; color:#daa;">%ended%</p>
<p style='text-align:center;color:#aaa;margin:0;letter-spacing:0.1em;' class="past_event">%mainfield%</p>
<p style='text-align:center;color:#aaa;margin:0;' class="past_event"><b>%field3%</b></p>
<p style='color:#aaa;;margin:0;'><b>%date%</b> %time% %field1% %infolink%</p>
</div>
