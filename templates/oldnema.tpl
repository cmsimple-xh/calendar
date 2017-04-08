CMSimple Calendar 1.4 Event-List Template
===<!-- 1 - headline annoncing the time period shown-->
<p style="font-size:80%;text-align:center;margin:2em 0;"><span style="letter-spacing:.4em;text-transform: uppercase;">%start% </span> %till%<span style="letter-spacing:.4em;text-transform: uppercase">  %end%</span></p>
===<!-- 2 - headline weekly section-->
<h6 style='text-align:center;margin:1em 0 0 0;padding:0;color:#084;letter-spacing:.5em;'>%weekly%</h6>
===<!-- 3 - weekday header-->
<p style='text-align:left;margin:.2em 0 0;'><b>%longweekday%</b></p>
===<!-- 4 - weekly events-->
<div style='
    border:1px solid #2f4f4f;
    margin:0 0 15px 0;
	padding:10px;
	background:#FAFAEF;
    border-radius: 5px;
    box-shadow: 5px 5px 5px #888;'>
<p style='float:right;'> %infolink% %field1%</p>
<p>%shortweekday% <b>%time%</b>     %date%</p>
<p style='color:#900;font-weight:bold;'>%mainfield%</p>
<div style='float:right;letter-spacing:.5em;font-weight:bold;text-transform: uppercase; color:#999;'>%bookedout%</div>
<p>%field3%</p>
<div style='margin-bottom:1em;'>%description%</div>
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
<h6 style='text-align:center;margin:2em 0 1em;padding:0;color:#084;letter-spacing:.5em;'>%single%</h6>
===<!-- 7 - month header-->
<p style='text-align:left;margin:.2em 0;'><b>%month% %year%</b></p>
===<!-- 8 - yearly events with age calculation -->
<div style='
    border:1px solid #2f4f4f;
    margin:0 0 15px 0;
	padding:0 10px 10px;
	background:#FAFAEF;
    border-radius: 5px;
    box-shadow: 5px 5px 5px #888;'>
<p style='text-align:center;color:#963;font-weight:bold;letter-spacing:.1em;text-transform:uppercase;font-size:90%;'>%age%<p>
<p style='float:right;'> %infolink% %field1%</p>
<p><b>%date%</b>   %time%</p>
<p style='color:#900;font-weight:bold;'>%mainfield%</p>
<div style='float:right;letter-spacing:.5em;font-weight:bold;text-transform: uppercase; color:#999;'>%bookedout%</div>
<p>%field3%</p>
<div style='margin-bottom:1em;'>%description%</div>
<div style='clear:both;'></div>
</div>
===<!-- 9 - yearly events -->
<div style='
    border:1px solid #2f4f4f;
    margin:0 0 15px 0;
	padding:10px;
	background:#FAFAEF;
    border-radius: 5px;
    box-shadow: 5px 5px 5px #888;'>
<p style='float:right;'> %infolink% %field1%</p>
<p><b>%date%</b>   %time%</p>
<p style='color:#900;font-weight:bold;'>%mainfield%</p>
<div style='float:right;letter-spacing:.5em;font-weight:bold;text-transform: uppercase; color:#999;'>%bookedout%</div>
<p>%field3%</p>
<div style='margin-bottom:1em;'>%description%</div>
<div style='clear:both;'></div>
</div>
===<!-- 10 - past yearly events-->
<p style="color:#888;" class="past_event">%ended%: %date%, %time%, %mainfield% %field3% %field1% %infolink%</p>
<p style='text-align:center;letter-spacing:-.3em;'>––––––––––––––––––</p>
===<!-- 11 - single events-->
<div style='
    border:1px solid #2f4f4f;
    margin:0 0 15px 0;
	padding:10px;
	background:#FAFAEF;
    border-radius: 5px;
    box-shadow: 5px 5px 5px #888;'>
<p style='float:right;'> %infolink% %field1%</p>
<p><b>%date%</b>   %time%</p>
<p style='color:#900;font-weight:bold;'>%mainfield%</p>
<div style='float:right;letter-spacing:.5em;font-weight:bold;text-transform: uppercase; color:#999;'>%bookedout%</div>
<p>%field3%</p>
<div style='margin-bottom:1em;'>%description%</div>
<div style='clear:both;'></div>
</div>
===<!-- 12 - past single events-->
<p style="color:#888;" class="past_event">%ended%: %date%, %time%,  %mainfield% %field3% %field1% %infolink%</p>
<p style='text-align:center;letter-spacing:-.3em;'>––––––––––––––––––</p>
