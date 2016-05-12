<?php header ("Content-Type:text/xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<rsm:CrossIndustryDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:rsm="urn:ferd:CrossIndustryDocument:invoice:1p0" xmlns:ram="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12" xmlns:udt="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:15">

    <rsm:SpecifiedExchangedDocumentContext>
        <ram:GuidelineSpecifiedDocumentContextParameter>
        <ram:ID>urn:ferd:CrossIndustryDocument:invoice:1p0:comfort</ram:ID>
        </ram:GuidelineSpecifiedDocumentContextParameter>
    </rsm:SpecifiedExchangedDocumentContext>

    <?php // header ?>
    <rsm:HeaderExchangedDocument>
        <ram:ID><?php echo htmlspecialchars($invoice->invoice_number); ?></ram:ID>
        <ram:Name><?php echo lang('invoice') ?></ram:Name>
        <ram:TypeCode>380</ram:TypeCode>
        <ram:IssueDateTime><udt:DateTimeString format="102"><?php echo zugferd_date_from_mysql($invoice->invoice_date_created); ?></udt:DateTimeString></ram:IssueDateTime>
        <ram:IncludedNote>
            <ram:Content><?php echo htmlspecialchars($invoice->invoice_terms); ?></ram:Content>
        </ram:IncludedNote>
    </rsm:HeaderExchangedDocument>

    <rsm:SpecifiedSupplyChainTradeTransaction>
        <ram:ApplicableSupplyChainTradeAgreement>
            <?php // user (seller) ?>
            <ram:SellerTradeParty>
                <ram:Name><?php echo htmlspecialchars($invoice->user_name); ?></ram:Name>
                <ram:PostalTradeAddress>
                    <ram:PostcodeCode><?php echo htmlspecialchars($invoice->user_zip); ?></ram:PostcodeCode>
                    <ram:LineOne><?php echo htmlspecialchars($invoice->user_address_1); ?></ram:LineOne>
                    <ram:LineTwo><?php echo htmlspecialchars($invoice->user_address_2); ?></ram:LineTwo>
                    <ram:CityName><?php echo htmlspecialchars($invoice->user_city); ?></ram:CityName>
                    <ram:CountryID><?php echo htmlspecialchars($invoice->user_country); ?></ram:CountryID>
                </ram:PostalTradeAddress>
            </ram:SellerTradeParty>

            <?php // client (buyer) ?>
            <ram:BuyerTradeParty>
                <ram:Name><?php echo $invoice->client_name; ?></ram:Name>
                <ram:PostalTradeAddress>
                    <ram:PostcodeCode><?php echo htmlspecialchars($invoice->client_zip); ?></ram:PostcodeCode>
                    <ram:LineOne><?php echo htmlspecialchars($invoice->client_address_1); ?></ram:LineOne>
                    <ram:LineTwo><?php echo htmlspecialchars($invoice->client_address_2); ?></ram:LineTwo>
                    <ram:CityName><?php echo htmlspecialchars($invoice->client_city); ?></ram:CityName>
                    <ram:CountryID><?php echo htmlspecialchars($invoice->client_country); ?></ram:CountryID>
                </ram:PostalTradeAddress>
                <ram:SpecifiedTaxRegistration>
                    <ram:ID schemeID="VA"><?php echo htmlspecialchars($invoice->client_vat_id); ?></ram:ID>
                </ram:SpecifiedTaxRegistration>
                <ram:SpecifiedTaxRegistration>
                    <ram:ID schemeID="FC"><?php echo htmlspecialchars($invoice->client_tax_code); ?></ram:ID>
                </ram:SpecifiedTaxRegistration>
            </ram:BuyerTradeParty>
        </ram:ApplicableSupplyChainTradeAgreement>

        <ram:ApplicableSupplyChainTradeDelivery>
            <ram:ActualDeliverySupplyChainEvent>
                <ram:OccurrenceDateTime><udt:DateTimeString format="102"><?php echo zugferd_date_from_mysql($invoice->invoice_date_created); ?></udt:DateTimeString> </ram:OccurrenceDateTime>
            </ram:ActualDeliverySupplyChainEvent>
        </ram:ApplicableSupplyChainTradeDelivery>

        <ram:ApplicableSupplyChainTradeSettlement>
            <ram:PaymentReference><?php echo htmlspecialchars($invoice->invoice_number); ?></ram:PaymentReference>
            <ram:InvoiceCurrencyCode><?php echo currency_code(); ?></ram:InvoiceCurrencyCode>

            <?php // taxes ?>
            <?php foreach (items_subtotal_grouped_by_tax_percent($items) as $percent=>$subtotal) { ?>
                <ram:ApplicableTradeTax>
                    <ram:CalculatedAmount currencyID="<?php echo currency_code(); ?>"><?php echo zugferd_float($subtotal * $percent / 100) ; ?></ram:CalculatedAmount>
                    <ram:TypeCode>VAT</ram:TypeCode>
                    <ram:BasisAmount currencyID="<?php echo currency_code(); ?>"><?php echo zugferd_float($subtotal); ?></ram:BasisAmount>
                    <ram:CategoryCode>S</ram:CategoryCode>
                    <ram:ApplicablePercent><?php echo $percent; ?></ram:ApplicablePercent>
                </ram:ApplicableTradeTax>
            <?php } ?>

            <?php // sums ?>
            <ram:SpecifiedTradeSettlementMonetarySummation>
                <ram:LineTotalAmount currencyID="<?php echo currency_code(); ?>"><?php echo $invoice->invoice_item_subtotal; ?></ram:LineTotalAmount>
                <ram:ChargeTotalAmount currencyID="<?php echo currency_code(); ?>">0.00</ram:ChargeTotalAmount>
                <ram:AllowanceTotalAmount currencyID="<?php echo currency_code(); ?>">0.00</ram:AllowanceTotalAmount>
                <ram:TaxBasisTotalAmount currencyID="<?php echo currency_code(); ?>"><?php echo $invoice->invoice_item_subtotal; ?></ram:TaxBasisTotalAmount>
                <ram:TaxTotalAmount currencyID="<?php echo currency_code(); ?>"><?php echo $invoice->invoice_item_tax_total; ?></ram:TaxTotalAmount>
                <ram:GrandTotalAmount currencyID="<?php echo currency_code(); ?>"><?php echo $invoice->invoice_total; ?></ram:GrandTotalAmount>
                <ram:TotalPrepaidAmount currencyID="<?php echo currency_code(); ?>"><?php echo $invoice->invoice_paid; ?></ram:TotalPrepaidAmount>
                <ram:DuePayableAmount currencyID="<?php echo currency_code(); ?>"><?php echo $invoice->invoice_balance; ?></ram:DuePayableAmount>
            </ram:SpecifiedTradeSettlementMonetarySummation>

        </ram:ApplicableSupplyChainTradeSettlement>

        <?php // items ?>
        <?php foreach ($items as $index=>$item) { ?>
            <ram:IncludedSupplyChainTradeLineItem>
                <ram:AssociatedDocumentLineDocument>
                    <ram:LineID><?php echo $index + 1 ?></ram:LineID>
                </ram:AssociatedDocumentLineDocument>
                <ram:SpecifiedSupplyChainTradeAgreement>
                    <ram:GrossPriceProductTradePrice>
                        <ram:ChargeAmount currencyID="<?php echo currency_code(); ?>"><?php echo zugferd_float($item->item_price, 4); ?></ram:ChargeAmount>
                    </ram:GrossPriceProductTradePrice>
                    <ram:NetPriceProductTradePrice>
                        <ram:ChargeAmount currencyID="<?php echo currency_code(); ?>"><?php echo zugferd_float($item->item_price, 4); ?></ram:ChargeAmount>
                    </ram:NetPriceProductTradePrice>
                </ram:SpecifiedSupplyChainTradeAgreement>
                <ram:SpecifiedSupplyChainTradeDelivery>
                    <ram:BilledQuantity unitCode="C62"><?php echo zugferd_float($item->item_quantity, 4); ?></ram:BilledQuantity>
                </ram:SpecifiedSupplyChainTradeDelivery>
                <ram:SpecifiedSupplyChainTradeSettlement>
                    <ram:ApplicableTradeTax>
                        <ram:TypeCode>VAT</ram:TypeCode>
                        <ram:ApplicablePercent><?php echo $item->item_tax_rate_percent; ?></ram:ApplicablePercent>
                    </ram:ApplicableTradeTax>
                    <ram:SpecifiedTradeSettlementMonetarySummation>
                        <ram:LineTotalAmount currencyID="<?php echo currency_code(); ?>"><?php echo $item->item_subtotal; ?></ram:LineTotalAmount>
                    </ram:SpecifiedTradeSettlementMonetarySummation>
                </ram:SpecifiedSupplyChainTradeSettlement>
                <ram:SpecifiedTradeProduct>
                    <ram:Name><?php echo htmlspecialchars($item->item_name . "\n" . $item->item_description); ?></ram:Name>
                </ram:SpecifiedTradeProduct>
            </ram:IncludedSupplyChainTradeLineItem>
        <?php } ?>

    </rsm:SpecifiedSupplyChainTradeTransaction>
</rsm:CrossIndustryDocument>
