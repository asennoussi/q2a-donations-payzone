<?php
class qa_donations_payzone_thank {
    function match_request($request){
        $parts=explode('/', $request);
        return $parts[0]=='thank-you';
    }
    function process_request($request)
    {
        $qa_content=qa_content_prepare();
        $qa_content['title']=qa_lang_html('plugin_donations_payzone/donation_thank_title');
        $qa_content['custom'] = qa_lang_html('plugin_donations_payzone/thank_you_after_donation').'<br><div id="heart"></div>';
        return $qa_content;
    }
}
