<?php
// the shortcode
function ssc_shortcode($atts) {
?>
<script type="text/javascript">
var templateUrl = '<?= get_bloginfo("url"); ?>';

jQuery( function( $ )
{
	$( 'a#main-solar-calculator-button' ).click( function( e )
	{
		e.preventDefault();
		var postcode = $( '#solar-postcode-input' ).val();
		if ( typeof postcode != 'undefined' )
			window.location.href = $( this ).attr( 'href' ) + '?postcode=' + postcode;
	} );
} );
</script>
<div class="page-section">
	<div class="header-title"><h1 style="text-transform: uppercase">Solar Power Savings Calculator</h1></div>
    <div class="clearboth"></div>
	<div class="page-section-inner">
		<form id="solarCalc" class="solarCalculator" onSubmit="return false;">
			<div class="solarcalc-params">
				<div class="calcMode">
					<label>Mode</label>
			    <span class="toggle-btn-group toggle-btn-group-2">
	          <input type="radio" name="calcMode" value="simple" id="solarcalc-mode-simple" checked/><label for="solarcalc-mode-simple">Simple</label>
	          <input type="radio" name="calcMode" value="advanced" id="solarcalc-mode-advanced"/><label for="solarcalc-mode-advanced">Advanced</label>
			    </span>
				</div>

<!-- User Details eg Location etc -->

				<fieldset class="yourDetails firld-position">
                <div class="sol_top_view_position">
					<legend class="font-style titleposition1">Electricity Bill Details</legend>
					<div class="flex-text back-style-1 marg-div1">
						<label class="field-label">Postcode</label>
						<span class="input span-wight"><input type="text" id="solar-postcode-input" name="postcode" value="2000" placeholder="0000" autocomplete="off" maxlength="10" minlength="10"/></span>
						<div class="msg-position">
                        <div class="info-tooltip">
                        <span class="info-tooltip-msg">Your postcode determines you location and thus the solar radiation your system will receive. It also determines which electricity retailers you can choice from.</span>
                        </div>
                        </div>
					</div>
                    <div class="flex-text back-style-1 marg-div1">
						<label class="field-label">Retail Price</label>
						<span class="input span-wight2"><input style="width:42px; height:26px;" type="text" name="retailPrice" value="" placeholder="cents" autocomplete="off" maxlength="2" minlength="1"/><small>c/kWh</small></span>
						<label class="field-label field-label-inline">Feed In Tariff</label>
						<span class="leftposi"><input style="width:42px; height:26px; margin-top:4px;" type="text" name="fitPrice" value="" placeholder="cents" autocomplete="off" maxlength="2" minlength="1"/></span>
                        <div class="msg-position">
						<div class="info-tooltip">
							<span class="info-tooltip-msg">This is determined by which retailer you're with and which state you live in.</span>
						</div>
                        </div>
					</div>
					<div class="flex-text  back-style-1 marg-div1">
						<label class="field-label">Retailer</label>
						<span class="input span-wight">
							<select name="retailer">
								<option value="Energy Australia">Energy Australia</option>
                                <option value="AGL">AGL</option>
                                <option value="Origin">Origin</option>
                                <option value="Red Energy">Red Energy</option>
                                <option value="Lumo">Lumo</option>
                                <option value="Power Direct">Power Direct</option>
                                <option value="Australian Power and Gas">Australian Power and Gas</option>
                                <option value="Dodo">Dodo</option>
                                <option value="Country Energy">Country Energy</option>
                                <option value="Integral">Integral</option>
                                <option value="TRUenergy">TRUenergy</option>
							</select>
						</span>
                        <div class="msg-position">
						<div class="info-tooltip">
							<span class="info-tooltip-msg">Your retailer is important as this determines what you pay for your power from the grid, plus what you’ll be paid for your feed in tariff and the rules surrounding it. You can find out how your current retailer compares to others</span>
						</div>
                        </div>
					</div>
                    <div class="flex-text back-style-1 marg-div1">
						<label class="field-label">Last Bill ($)</label>
						<span class="input span-wight">
							<input type="text" name="lastBillAmount" value="250" placeholder="$0.00"/>
							<select name="billingPeriod">
								<option value="monthly">Monthly</option>
								<option value="bimonthly" SELECTED>Bi-Monthly</option>
								<option value="quarterly">Quarterly</option>
							</select>
						</span>
                        <div class="msg-position">
						<div class="info-tooltip">
							<span class="info-tooltip-msg">This helps determine how much you can save and what your new bill will be with different system sizes.</span>
						</div>
                        </div>
					</div>
                    <div class="flex-text back-style-1 marg-div1">
						<label class="field-label text-style">System Price ($)</label>
						<span class="input span-wight">
							<input type="text" name="systemPrice" value="4000" placeholder="$0.00"/>
						</span>
                        <div class="msg-position">
						<div class="info-tooltip">
							<span class="info-tooltip-msg">The price automatically populated is the median price in the market at the moment for the selected system size. There may be cheaper systems around which will be of lesser quality or higher quality systems which are more expensive. We recommend paying slightly more for quality, as the system needs to be operating for many years to come in quite extreme weather conditions.</span>
						</div>
                        </div>
					</div>
                    </div>
					<div class="billInflation all-billInflation advancedMode flex-text back-style-1">
						<label class="field-label">Bill Inflation</label>
						<span class="input span-wight">
							<input style="left:33px; top:-3px;" type="text" name="billInflation" value="5" /> <small style="margin-left:30px;">% / annum</small>
						</span>
                        <!--<div class="msg-position">
						<div class="info-tooltip">
							<span class="info-tooltip-msg">We've assumed 5% inflation of electricity prices per annum. Going on the last few years though where some state government’s have increased prices by in excess of 20%, 5% may be a bit conservative.</span>
						</div>
                        </div>-->
					</div>
                    <div class="advancedMode systemDegredation flex-text back-style-1  marg-div1">
						<label class="field-label text-style">System Degredation</label>
						<span class="input span-wight"><input style="left:33px; top:-3px;" type="text" name="systemDegradation" value="0.5" /> <small style="margin-left:30px;">% / annum</small></span>
                    <!--<div class="msg-position">
				    <div class="info-tooltip">
					    <span class="info-tooltip-msg">
						    <p>This is the percentage of electricity produced by your system which is exported to the grid and which you'll be paid the feed in tariff for.</p>
								<p>If you're home and using power during the day, you'll use the power your system produces first and therefore export less.</p>
								<p>If most of the household is at work or school during the day, then you’re export percentage will be higher.</p>
								<p>Larger systems will export more than smaller systems.</p>
								<p>If you're feed in tariff is less than what you pay for your power, which is the case in most states, then you should try to shift your power usage to the middle of the day rather than evening. This can be done by using washing machines, dishwashers, dryers etc during the middle of the day.</p>
				      </span>
				    </div>
                    </div>-->
					</div>
                    <div class="feedinPercentage advancedMode" oninput="vFeedInPercentage.value = feedinPercentage.valueAsNumber+'%'">
						<label class="field-label posi-size font-style">Feed in Percentage</label>
						<div class="flex-range" >
							<span class="min posi-left">5%</span>
							<span class="input">
								<input type="range" id="solarcalc-feedinPercentage" name="feedinPercentage" min="5" max="80" step="1" value="26"/>
								<output for="solarcalc-feedinPercentage" name="vFeedInPercentage">80%</output>
							</span>
							<span class="max posi-right">80%</span>
					</div>
                    </div>
                    <div class="powerUsage simpleMode highest-energy">
                    <div class="sol-field2">
                    	<h4>real people, real testimonials</h4>
                        <div class="sol-real-pro">
                        <img style="display: block;" class="lazyload" src="<?= get_bloginfo("url"); ?>/wp-content/plugins/solarcal/images/customer.png" alt="Customer" title="Customer">
                        <p class="sol-expra-color" style="color:#0080F8; font-size:14px;">Thank you Australian Solar Quoes</p>
                        <p>I had been to a few other websites when I began looking to have solar panels installed in my house. And, out of all the ones I found, none could compare to the service Australian Solar Quotes provided.</p>
                        </div>
                    </div>
							<label class="field-label posi-size font-style">Highest Energy Consumption Period</label>
							<div class="flex-text">
						    <span class=" input toggle-btn-group toggle-btn-group-3">
						      <input type="radio" name="usage" value="allday" id="solarcalc-usage-allday" /><label for="solarcalc-usage-allday">Day</label>
							    <input type="radio" name="usage" value="midday" id="solarcalc-usage-midday"  checked="checked" /><label for="solarcalc-usage-midday">Midday</label>
							    <input type="radio" name="usage" value="evenings" id="solarcalc-usage-evenings" /><label for="solarcalc-usage-evenings">Evenings</label>
							  </span>
							</div>
					</div>
				</fieldset>
                <div class="clearboth"></div>
				<fieldset class="solar-system-details fild-position2">
					<legend class="font-style">Solar System Details</legend>
                    <div class="systemSize" oninput="document.getElementsByName('vSystemSize')[0].value = document.getElementsByName('size')[0].valueAsNumber+'kW'">
						<div class="flex-range">
                            <div class="ssc_pitch ssc-field1">
                                <h4 class="ssc-top-border">Size of system</h4>
                                <div class="ssc-house-position">
                                    <div class="ssc-preview">
                                    	<span></span>
                                	</div>
                            	</div>
							<span class="min posi-left">1.5kW</span>
							<span class="input">
								<input type="range" id="solarcalc-size" name="size" min="1.4" max="7.0" value="3.0" step="0.2"/>
								<output for="solarcalc-size" name="vSystemSize">3.0kW</output>
							</span>
							<span class="max posi-right">7.0kW</span>
						</div>
					</div>
                    </div>
				</fieldset>
				<fieldset class="systemInstallation fild-position2" >
					<legend class="font-style">System Installation</legend>
					<div class="systemOrientation">
						<div class="roofOrientation">

							<div class="flex-label">
								<label class="field-label">Orientation</label>
								<div class="msg-position">
                                <div class="info-tooltip">
									<div class="info-tooltip-msg">
								    <p>Orientation has quite a large effect on production. North is optimal and each movement away form north will mean less production.</p>
								    <table>
									    <tr><th>Roof Orientation</th><th>Production Loss</th></tr>
									    <tr><td>North</td><td>no losses</td></tr>
									    <tr><td>North East/North West</td><td>7% loss</td></tr>
									    <tr><td>East/West</td><td>15% loss</td></tr>
									    <tr><td>South</td><td>38% loss</td></tr>
								    </table>
						      </div>
								</div>
                                </div>
							</div>
							<div class="orientation">
								<div class="orientation-preview">
									<input type="hidden" name="orientation" id="orientation-value" value="N"/>
									<span class="house"></span>
                                    <span class="N">N 0</span>
                                    <span class="NE">NE 45</span>
                                    <span class="E">E 90</span>
                                    <span class="SE">SE 135</span>
                                    <span class="S">S 180</span>
                                    <span class="SW">SW 225</span>
                                    <span class="W">W 270</span>
                                    <span class="WN">WN 315</span>
                                    <span class="N">N 0</span>
								</div>
								<div class="orientation-control" oninput="var values=['N','NE','E','SE','S','SW','W','WN','N'];document.getElementById('solarcalc-orientation-slider-label').value=document.getElementById('orientation-value').value=values[document.getElementById('solarcalc-orientation-slider').value];">
									<input type="range" name="orientationslider" id="solarcalc-orientation-slider" min="0" max="8" value="5"/>
									<output for="solarcalc-orientation-slider" id="solarcalc-orientation-slider-label" name="vOrientationSlider">N</output>
								</div>
							</div>
						</div>
						<div class="roofPitch">
							<div class="flex-label">
								<label class="field-label">Roof Pitch</label>
								<div class="msg-position">
                                <div class="info-tooltip">
									<div class="info-tooltip-msg">
									  <p>The angle of your roof doesn't massively effect your system's production. To get the best production, your roof should be pitched at the same degrees as the latitude of your location. For example, Brisbane's coordinates are as follows – Latitude 27°25'S – Longitude 153° 9' E</p>
										<p>This means that the best roof pitch is 27 degrees which is about what most roofs are.</p>
										<p>If your pitch is different to this, don't worry, for every 5 degrees difference from this, you lose 1% production. So if the panels were laid flat, you’d only lose about 5% production.</p>
									</div>
								</div>
                                </div>
							</div>
							<span class="pitch">
							  <div class="preview"><span></span></div>
							  <div class="pitch-control" oninput="document.getElementById('solarcalc-pitch-label').value=document.getElementById('solarcalc-pitch').value+'%'">
								  <input type="range" vertical id="solarcalc-pitch" name="pitch" min="0" max="50" step="10" value="40"/>
								  <output for="solarcalc-pitch" id="solarcalc-pitch-label" name="vPitch">40%</output>
								</div>
						  </span>
						</div>
					</div>
				</fieldset>
			</div>
            <div class="clearboth"></div>
			<div class="solarcalc-results">
				<div id="savingsGraph">
					<div class="graph-container">
						<div class="graphBody">
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
							<div class="bar"><div title="" style="height:0%;"></div><div title="" style="height:0%;"></div></div>
						</div>
						<div class="legend">
							<div>Without Solar</div>
							<div>With Solar</div>
						</div>
						<div class="graphXAxis">
							<span>1</span>
							<span>2</span>
							<span>3</span>
							<span>4</span>
							<span>5</span>
							<span>6</span>
							<span>7</span>
							<span>8</span>
							<span>9</span>
							<span>10</span>
							<span>11</span>
							<span>12</span>
							<span>13</span>
							<span>14</span>
							<span>15</span>
							<span>16</span>
							<span>17</span>
							<span>18</span>
							<span>19</span>
							<span>20</span>

						</div>
						<div class="graphYAxis"></div>
						<span class="status">Calculating...</span>
					</div>
				</div>
				<div id="calcResults">
					<div class="results-section">
						<fieldset class="estimated-production">
							<legend class="leg-position">Estimated Electricity Production</legend>
							<div class="flex-text">
								<label class="result-label">Solar Power Production</label>
								<span class="input field-size argest-ment"><strong id="productionKW">&nbsp;</strong>/yr</span>
							</div>
							<div class="flex-text">
								<label class="result-label">&nbsp;</label>
								<span class="input field-size"><strong id="dayProductionKW">&nbsp;</strong>/day</span>
								<!--<span class="info-tooltip-placeholder">&nbsp;</span>-->
							</div>
							<div class="flex-text">
								<label class="result-label">Electricity fed back to grid</label>
								<span class="input field-size"><strong id="feedInKW">&nbsp;</strong>/yr</span>
								<!--<span class="info-tooltip-placeholder">&nbsp;</span>-->
							</div>
						</fieldset>
						<fieldset style="margin-top:8px;">
							<legend class="result-label leg-position investment">Investment</legend>
							<div class="flex-text">
								<label class="result-label">System Price</label>
								<span class="input field-size"><strong id="systemPrice">&nbsp;</strong></span>
								<!--<span class="info-tooltip-placeholder">&nbsp;</span>-->
							</div>
							<div class="flex-text">
								<label class="result-label">Saving power bill</label>
								<span class="input field-size"><strong class="electricitySavingYr">&nbsp;</strong>/yr</span>
							</div>
							<div class="flex-text">
								<label class="result-label">Payback Period</label>
								<span class="input field-size"><strong class="paybackPeriod">&nbsp;</strong></span>
							</div>
							<div class="flex-text">
								<label class="result-label">Return on Investment</label>
								<span class="input field-size"><strong class="returnOnInvestment">&nbsp;</strong></span>
							</div>
						</fieldset>
					</div>
					<div class="results-section annualBills">
						<fieldset>
							<legend class="leg-position">Reduced Energy Costs</legend>
							<div class="flex-text">
								<label class="result-label">Price you currently pay/kWh</label>
								<span class="input field-size"><strong class="retailerPriceKWH">&nbsp;</strong></span>
							</div>
							<div class="flex-text">
								<label class="result-label">Effective price/ kWh after installing solar</label>
								<span class="input field-size"><strong class="effectivePriceKWH">&nbsp;</strong></span>
							</div>
							<div class="flex-text">
								<label class="result-label">Savings / kWh</label>
								<span class="input field-size"><strong class="savingPriceKWH">&nbsp;</strong></span>
							</div>
						</fieldset>
						<fieldset>
							<legend class="leg-position">Savings on Electricity Bills</legend>
							<div class="flex-text calcYears">
								<label class="result-label">Savings over</label>
						    <span class="toggle-btn-group toggle-btn-group-4 bt-show">
					        <input type="radio" name="yrs" id="solarcalc-yrs-1" value="1"/><label for="solarcalc-yrs-1">1</label>
					        <input type="radio" name="yrs" id="solarcalc-yrs-5" value="5"/><label for="solarcalc-yrs-5">5</label>
					        <input type="radio" name="yrs" id="solarcalc-yrs-10" value="10" checked/><label for="solarcalc-yrs-10">10</label>
					        <input type="radio" name="yrs" id="solarcalc-yrs-20" value="20"/><label for="solarcalc-yrs-20">20</label>
							  </span>
							</div>
							<div class="flex-text">
								<label class="result-label">Electricity bill (without solar)</label>
								<span class="input field-size"><strong id="electricityCostsNoSolar">&nbsp;</strong></span>
							</div>
							<div class="flex-text">
								<label class="result-label">Electricity bill (with solar)</label>
								<span class="input field-size"><strong id="electricityCostsSolar">&nbsp;</strong></span>
							</div>
							<div class="flex-text">
								<label class="result-label">Electricity bill savings</label>
								<span class="input field-size"><strong id="electricitySavings">&nbsp;</strong></span>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
            <div class="clearboth"></div>
		</form>
	</div>
    <div class="clearboth"></div>
    <div class="btn btn-free-quote"><a id="main-solar-calculator-button" href="https://www.australiansolarquotes.com.au/fba-solar-quotes/">Click Here to Get 3 Quotes</a></div>
</div>
<?php 
}
add_shortcode('solar_cal_content', 'ssc_shortcode');
?>