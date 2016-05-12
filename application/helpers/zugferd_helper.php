<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
 */

function generate_invoice_zugferd_xml($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('invoices/mdl_invoices');
    $CI->load->model('invoices/mdl_items');

    $data = array(
        'invoice' => $CI->mdl_invoices->get_by_id($invoice_id),
        'items' => $CI->mdl_items->where('invoice_id', $invoice_id)->get()->result()
    );

    $CI->load->view('invoice_templates/xml/zugferd.php', $data);
}

function generate_invoice_zugferd_xml_temp_file($invoice, $items){
    $CI = &get_instance();
    $data = array(
        'invoice' => $invoice,
        'items' => $items
    );

    $xml = $CI->load->view('invoice_templates/xml/zugferd.php', $data, TRUE);
    $path = './uploads/temp/invoice_' . $invoice->invoice_id . '_zugferd.xml';
    $CI->load->helper('file');
    write_file($path, $xml);
    return $path;
}

function items_subtotal_grouped_by_tax_percent($items){
    $result = [];
    foreach ($items as $item) {
        if (!isset($result[$item->item_tax_rate_percent])) {
            $result[$item->item_tax_rate_percent] = 0;
        }
        $result[$item->item_tax_rate_percent] += $item->item_subtotal;
    }
    return $result;
}

function ZugferdRdf(){
    $s  = '<rdf:Description rdf:about="" xmlns:zf="urn:ferd:pdfa:CrossIndustryDocument:invoice:1p0#">'."\n";
    $s .= '  <zf:DocumentType>INVOICE</zf:DocumentType>'."\n";
    $s .= '  <zf:DocumentFileName>ZUGFeRD-invoice.xml</zf:DocumentFileName>'."\n";
    $s .= '  <zf:Version>1.0</zf:Version>'."\n";
    $s .= '  <zf:ConformanceLevel>COMFORT</zf:ConformanceLevel>'."\n";
    $s .= '</rdf:Description>'."\n";
return $s;
}

/*
 * returns a zugferd formatted date (YYYYMMDD)
 */
function zugferd_date_from_mysql($date)
{
    if ($date <> '0000-00-00') {
      $date = DateTime::createFromFormat('Y-m-d', $date);
      return $date->format('Ymd');
    }
    return '';
}

/*
 * returns a zugferd formatted number with given decimals (2 by default)
 */
function zugferd_float($amount, $nb_decimals = 2)
{
    return number_format((float)$amount, $nb_decimals);
}

function currency_code()
{
    global $CI;
    return $CI->mdl_settings->setting('currency_code');
}
