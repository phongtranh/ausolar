<?php
function ssc_shortcode( $atts )
{
    ?>
    <div class="solar-calculator" ng-app="Calculator">

        <header class="header-title">
            <h1 style="text-transform: uppercase">Solar Power Savings Calculator</h1>
        </header>

        <div class="solar-calculator-inner">
            <form method="get" action="/" class="calculator-form" ng-controller="CalculatorController">
                <div class="calculator-data">

                    <div class="mode">
                        <label class="control-label">Mode</label>
                        <div class="toggle-btn-group toggle-btn-group-2">
                            <input type="radio" ng-model="data.mode" value="simple" id="solarcalc-mode-simple" />
                            <label for="solarcalc-mode-simple">Simple</label>
                            <input type="radio" ng-model="data.mode" value="advanced" id="solarcalc-mode-advanced" />
                            <label for="solarcalc-mode-advanced">Advanced</label>
                        </div>
                    </div>

                    <div class="form-data row-fluid">
                        <div class="span6">
                            <h3>Electricity Bill Details</h3>
                            
                            <div class="flex-text back-style-1 marg-div1 control-group">
                                <label class="control-label" for="solar-postcode-input">Postcode</label>
                                <div class="input span-wight controls">
                                    <input class="span12" type="number" ng-model="data.postcode" id="solar-postcode-input" name="postcode" value="2000" placeholder="0000" autocomplete="off" />
                                </div>
                                <a class="calculator-tooltip" href="#" data-placement="bottom" data-toggle="tooltip" title="Your postcode determines you location and thus the solar radiation your system will receive. It also determines which electricity retailers you can choice from.">i</a>
                            </div>

                            <div class="flex-text back-style-1 marg-div1 retail-price">                                
                                <label class="control-label" for="data-retail-price">Retail Price</label>
                                <span class="controls">
                                	<input class="span6" id="data-retail-price" type="number" step="any" ng-model="data.retail_price" name="retailPrice" placeholder="cents" autocomplete="off" />
                                	<small>c/kWh</small>
                                </span>

                                <label class="control-label">Feed In Tariff</label>
                                <span class="controls">
                                	<input class="span6" type="number" step="any" ng-model="data.fit_price" name="fitPrice" placeholder="cents" autocomplete="off" />
                                </span>
								
								<a class="calculator-tooltip" href="#" data-placement="bottom" data-toggle="tooltip" title="This is determined by which retailer you're with and which state you live in.">i</a>
                            </div>

                            <div class="control-group flex-text back-style-1 marg-div1">
                                <label class="control-label">Retailer</label>
                                <span class="controls input span-wight">
                                    <select class="span12" ng-options="item.name as item.name for item in retailers" ng-model="data.retailer" ng-change="onRetailerChangeEventHandler()"></select>
                                </span>
								
								<a class="calculator-tooltip" href="#" data-placement="bottom" data-toggle="tooltip" title="Your retailer is important as this determines what you pay for your power from the grid, plus what you’ll be paid for your feed in tariff and the rules surrounding it. You can find out how your current retailer compares to others">i</a>
                            </div>
                            <div class="flex-text back-style-1 marg-div1 last-bill">
                                <label class="control-label">Last Bill ($)</label>
                                <span class="input span-wight">
                                    <input type="number" step="any" class="span4" ng-model="data.last_bill_amount" placeholder="$0.00"/>
                                    <select class="span8" ng-model="data.billing_period">
                                        <option value="monthly">Monthly</option>
                                        <option value="bimonthly">Bi-Monthly</option>
                                        <option value="quarterly">Quarterly</option>
                                    </select>
                                </span>
								
								<a class="calculator-tooltip" href="#" data-placement="bottom" data-toggle="tooltip" title="This helps determine how much you can save and what your new bill will be with different system sizes.">i</a>
                            </div>
                            <div class="flex-text back-style-1 marg-div1">
                                <label class="control-label">System Price ($)</label>
                                <span class="input span-wight">
                                    <input type="number" class="span12" step="any" ng-model="data.system_price" placeholder="$0.00"/>
                                </span>
								
								<a class="calculator-tooltip" href="#" data-placement="bottom" data-toggle="tooltip" title="The price automatically populated is the median price in the market at the moment for the selected system size. There may be cheaper systems around which will be of lesser quality or higher quality systems which are more expensive. We recommend paying slightly more for quality, as the system needs to be operating for many years to come in quite extreme weather conditions.">i</a>
                            </div>
                        </div>
						
						<div class="right-place span6">
	                        <div ng-show="data.mode == 'advanced'">
	                        	<h3>&nbsp;</h3>
		                        <div class="flex-text back-style-1 control-group">
		                            <label class="control-label">Bill Inflation</label>
									<span class="input span-wight controls">
										<input class="span4" type="number" step="any" name="bill_inflation" ng-model="data.bill_inflation" /> 
										<small style="margin-left:30px;">% / annum
			                            </small>
									</span>
		                        </div>
		                        <div class="systemDegredation flex-text back-style-1 marg-div1 control-group">
		                            <label class="control-label">System Degredation</label>
		                            <span class="input span-wight controls">
		                            	<input class="span4" type="number" step="any" ng-model="data.system_degradation" /> 
		                               <small style="margin-left:30px;">% / annum</small>
		                            </span>
		                        </div>
		                        <div class="feedinPercentage">
		                            <label class="posi-size control-label">Feed in Percentage</label>

		                            <div class="flex-range">
		                                <span class="min range-left">5%</span>
										<span class="input">
											<input type="range" ng-model="data.feedin_percentage" class="span12" min="5" max="80" step="1" />
											<output>{{data.feedin_percentage}}%</output>
										</span>
		                                <span class="max range-right">80%</span>
		                            </div>
		                        </div>
	                        </div>

	                        <div ng-show="data.mode != 'advanced'">
	                            <div class="heading-testimonial">
	                                <h3>Real People, Real Testimonials</h3>
	                                <div class="sol-real-pro">
	                                    <img style="display: block;" class="lazyload"
	                                         src="<?php get_bloginfo("url"); ?>/wp-content/plugins/solarcal/images/customer.png"
	                                         alt="Customer" title="Customer">

	                                    <p style="color:#0080F8; font-size:14px;">Thank you Australian Solar Quotes</p>

	                                    <p>I had been to a few other websites when I began looking to have solar panels
	                                        installed in my house. And, out of all the ones I found, none could compare to
	                                        the service Australian Solar Quotes provided.</p>
	                                </div>
	                            </div>
	                            <label class="label-heading">Highest Energy Consumption Period</label>

	                            <div class="flex-text">
							    	<span class=" input toggle-btn-group toggle-btn-group-3">
								      	<input type="radio" ng-model="data.usage" value="allday" id="solarcalc-usage-allday"/><label
		                                    for="solarcalc-usage-allday">Day</label>
									    <input type="radio" ng-model="data.usage" value="midday" id="solarcalc-usage-midday"
		                                       checked="checked"/><label for="solarcalc-usage-midday">Midday</label>
									    <input type="radio" ng-model="data.usage" value="evenings" id="solarcalc-usage-evenings"/><label
		                                    for="solarcalc-usage-evenings">Evenings</label>
								  	</span>
	                            </div>
	                        </div>
						</div><!--.right-place-->
                    </div><!--.row-fluid-->
					
					<hr>
					<div class="row-fluid">

	                    <div class="solar-system-details fild-position2 span6">
	                        <h3>Size of System</h3>
	                        <div class="system-size">
	                            <div class="flex-range">
	                                <div class="ssc_pitch ssc-field1">
	                                    <div class="position">
	                                        <div class="calculator-preview">
	                                            <span style="background:url(/wp-content/plugins/solarcal/images/systemsize{{data.system_size}}.png) no-repeat"></span>
	                                        </div>
	                                    </div>
	                                    <span class="min range-left">1.5kW</span>
										<span class="input">
											<input type="range" id="solarcalc-size" ng-model="data.system_size" ng-change="onSystemSizeChangeEventHandler()" min="1.4" max="7.0" step="0.2" />
											<output for="solarcalc-size" name="vSystemSize">{{data.system_size}}kW</output>
										</span>
	                                    <span class="max range-right">7.0kW</span>
	                                </div>
	                            </div>
	                        </div>
	                    </div>

	                    <div class="systemInstallation span6">
	                        <h3>System Installation</h3>
	                        <div class="system-orientation">
	                            <div class="roof-orientation">

	                                <div class="flex-label">
	                                    <label class="control-label">Orientation</label>
											<a class="calculator-tooltip" href="#" data-html="true" data-placement="bottom" data-toggle="tooltip" title="<p>Orientation has quite a large effect on production. North is optimal
	                                                    and each movement away form north will mean less production.</p>
                                                <table>
                                                    <tr>
                                                        <th>Roof Orientation</th>
                                                        <th>Production Loss</th>
                                                    </tr>
                                                    <tr>
                                                        <td>North</td>
                                                        <td>no losses</td>
                                                    </tr>
                                                    <tr>
                                                        <td>North East/North West</td>
                                                        <td>7% loss</td>
                                                    </tr>
                                                    <tr>
                                                        <td>East/West</td>
                                                        <td>15% loss</td>
                                                    </tr>
                                                    <tr>
                                                        <td>South</td>
                                                        <td>38% loss</td>
                                                    </tr>
                                               </table>">i</a>
	                                </div>
	                                <div class="orientation">
	                                    <div class="orientation-preview">
	                                        <input type="hidden" name="orientation" id="orientation-value" value="{{orientations[data.orientation]}}"/>
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
	                                    <div class="orientation-control">
	                                        <input type="range" name="orientationslider" id="solarcalc-orientation-slider"
	                                               min="0" max="8" ng-model="data.orientation" />
	                                        <output for="solarcalc-orientation-slider" name="vOrientationSlider">{{orientations[data.orientation]}}</output>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="roof-pitch">
	                                <div class="flex-label">
	                                    <label class="control-label">Roof Pitch</label>
	                                    <a class="calculator-tooltip" data-html="true" href="#" data-placement="bottom" data-toggle="tooltip" title=" <p>The angle of your roof doesn't massively effect your system's
                                                    production. To get the best production, your roof should be pitched
                                                    at the same degrees as the latitude of your location. For example,
                                                    Brisbane's coordinates are as follows – Latitude 27°25'S – Longitude
                                                    153° 9' E</p>

                                                <p>This means that the best roof pitch is 27 degrees which is about what
                                                    most roofs are.</p>

                                                <p>If your pitch is different to this, don't worry, for every 5 degrees
                                                    difference from this, you lose 1% production. So if the panels were
                                                    laid flat, you’d only lose about 5% production.</p>">i</a>
	                                </div>
									<span class="pitch">
									  <div class="preview">
									  	<span style="background:url(/wp-content/plugins/solarcal/images/roof_pitch_{{data.pitch}}.png) no-repeat"></span>
									  </div>
									  <div class="pitch-control">
		                                  <input type="range" vertical id="solarcalc-pitch" name="pitch" min="0" max="50" step="10" value="40" ng-model="data.pitch" />
		                                  <output for="solarcalc-pitch" id="solarcalc-pitch-label" name="vPitch">{{data.pitch}}%</output>
		                              </div>
								  	</span>
	                            </div>
	                        </div>
	                    </div>
	                </div><!--.row-fluid-->
                </div><!--.calculator-data-->

                <div class="calculator-output">
                    <div id="chart">
                        <div id="bars">
                        	<div class="bar" ng-repeat="(year, value) in output.chart.bills">
                        		<div title="Bill without Solar = ${{value | number:2}}" style="height: {{(value-output.graph_min)/(output.graph_max-output.graph_min)*100}}%;"></div>
                                <div title="Bill with Solar = ${{output.chart.solar[year] | number:2}} (Savings ${{output.chart.savings[year] | number:2}})" style="height: {{(output.chart.solar[year]-output.graph_min)/(output.graph_max-output.graph_min)*100}}%;"></div>
                        	</div>
                        </div>
                        
                        <div class="legend">
                            <div>Without Solar</div>
                            <div>With Solar</div>
                        </div>

                        <div class="chart-x">
                        <?php for ( $i = 1; $i <= 20; $i++ )
                        {
                        	echo "<span>{$i}</span>";
                        }
                        ?>
                        </div>

                        <div class="chart-y">
                        	<span>{{output.graph_max | number:0}}</span>
							<span>{{output.graph_max * 0.75 | number:0}}</span>
							<span>{{output.graph_max / 2 | number:0}}</span>
							<span>{{output.graph_max / 4 | number:0}}</span>
							<span>{{output.graph_min}}</span>
                        </div>
                    </div>
                    <div id="result-stats" class="row-fluid">
                        <div class="span6">
                            <fieldset class="estimated-production">
                                <h3>Estimated Electricity Production</h3>
                                
                                <div class="flex-text control-group">
                                    <label class="control-label">Solar Power Production</label>
                                    <span class="uneditable-input span6">
                                    	<strong>{{output.solar_production | number:2}}kWh</strong>/yr
                                    </span>
                                </div>

                                <div class="flex-text">
                                    <label class="control-label">&nbsp;</label>
                                    <span class="uneditable-input span6">
                                    	<strong id="dayProductionKW">{{output.units_per_day | number:2}}kWh</strong>/day
                                    </span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Electricity fed back to grid</label>
                                    <span class="uneditable-input span6"><strong>{{output.solar_feedin | number:2}}</strong>/yr</span>
                                </div>
                            </fieldset>

                            <fieldset>
                                <h3 class="control-label">Investment</h3>
                                <div class="flex-text">
                                    <label class="control-label">System Price</label>
                                    <span class="uneditable-input span6"><strong>${{data.system_price | number:2}}</strong></span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Saving power bill</label>
                                    <span class="uneditable-input span6"><strong>${{output.power_savings | number:2}}</strong>/yr</span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Payback Period</label>
                                    <span class="uneditable-input span6"><strong>{{output.payback | number:2}} yrs</strong></span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Return on Investment</label>
                                    <span class="uneditable-input span6">
                                    	<strong class="returnOnInvestment">{{output.roi | number:2}}%</strong>
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                        <div class="span6">
                            <fieldset>
                                <h3>Reduced Energy Costs</h3>
                                <div class="flex-text">
                                    <label class="control-label">Price you currently pay/kWh</label>
                                    <span class="uneditable-input span6">
                                    	<strong class="retailerPriceKWH">{{data.retail_price | number:2}} c/kWh</strong>
                                    </span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Effective price/ kWh after installing solar</label>
                                    <span class="uneditable-input span6">
                                    	<strong class="effectivePriceKWH">{{output.eff_price_kilowatt | number:2}} c/kWh</strong>
                                    </span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Savings / kWh</label>
                                    <span class="uneditable-input span6">
                                    	<strong class="savingPriceKWH">{{data.retail_price-output.eff_price_kilowatt | number:2}} c/kWh</strong>
                                    </span>
                                </div>
                            </fieldset>
                            <fieldset>
                                <h3>Savings on Electricity Bills</h3>
                                <div class="flex-text calcYears">
                                    <label class="control-label">Savings over</label>
								    <span class="toggle-btn-group toggle-btn-group-4 bt-show">
								        <input type="radio" ng-model="data.years" id="solarcalc-yrs-1" value="1" />
								        <label for="solarcalc-yrs-1">1 yr</label>
								        <input type="radio" ng-model="data.years" id="solarcalc-yrs-5" value="5" />
								        <label for="solarcalc-yrs-5">5 yrs</label>
								        <input type="radio" ng-model="data.years" id="solarcalc-yrs-10" value="10" />
								        <label for="solarcalc-yrs-10">10 yrs</label>
								        <input type="radio" ng-model="data.years" id="solarcalc-yrs-20" value="20" />
								        <label for="solarcalc-yrs-20">20 yrs</label>
									  </span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Electricity bill (without solar)</label>
                                    <span class="uneditable-input span6">
                                    	<strong id="electricityCostsNoSolar">${{output.without_solar_bill | number:2}}</strong>
                                    </span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Electricity bill (with solar)</label>
                                    <span class="uneditable-input span6">
                                    	<strong id="electricityCostsSolar">${{output.with_solar_bill | number:2}}</strong>
                                    </span>
                                </div>
                                <div class="flex-text">
                                    <label class="control-label">Electricity bill savings</label>
                                    <span class="uneditable-input span6">
                                    	<strong id="electricitySavings"> ${{output.savings | number:2}}</strong>
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <a class="btn btn-primary btn-large btn-free-quote" id="main-solar-calculator-button" href="https://www.australiansolarquotes.com.au/fba-solar-quotes/">Click Here to Get 3 Quotes</a>
    </div><!--.calculator-->
<?php
}
add_shortcode('solar_cal_content', 'ssc_shortcode');