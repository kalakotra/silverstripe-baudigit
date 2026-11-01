<form $AttributesHTML>
	<% if $Message %>
	<p id="{$FormName}_error" class="message $MessageType text-danger">$Message</p>
	<% else %>
	<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
	<% end_if %>
	<fieldset>
		<% if $Legend %><legend>$Legend</legend><% end_if %>
		<div class="row align-items-end">
			<div class="col-12 col-lg-4 py-2">
				$Fields.dataFieldByName(Title).FieldHolder
			</div>
			<div class="col-12 col-lg-2 py-2">
                <div class="row g-0 align-items-end">
                    <div class="col-6">
				        $Fields.dataFieldByName(Quantity).FieldHolder
                    </div>
                    <div class="col-6">
				        $Fields.dataFieldByName(Unit).FieldHolder
                    </div>
                </div>
			</div>
			<div class="col-12 col-lg-6">
				<div class="row align-items-end">
					<div class="col-12 col-lg-3 py-2">
						$Fields.dataFieldByName(SellPriceNetto).FieldHolder
					</div>
					<div class="col-12 col-lg-3 py-2">
						$Fields.dataFieldByName(Discount).FieldHolder
					</div>
					<div class="col-12 col-lg-3 py-2">
						$Fields.dataFieldByName(TaxRate).FieldHolder
					</div>
					<div class="col-12 col-lg-3 py-2">
						$Fields.dataFieldByName(FinalPrice).FieldHolder
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-10 py-2">
				$Fields.dataFieldByName(Description).FieldHolder
			</div>
            <% loop $Actions %>
				<div class="col-12 col-lg-2 py-2">$Field</div>
			<% end_loop %>
		</div>
	</fieldset>
	
	$Fields.dataFieldByName(SecurityID)
	$Fields.dataFieldByName(e)
	$Fields.dataFieldByName(ea)
	$Fields.dataFieldByName(SourceArticleID)
</form>