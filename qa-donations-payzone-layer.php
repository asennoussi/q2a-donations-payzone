<?php
class qa_html_theme_layer extends qa_html_theme_base
{
    public function head_css()
    {
        $this->content['css_src'][]=qa_html(QA_HTML_THEME_LAYER_URLTOROOT.'/css/donations.css');
        qa_html_theme_base::head_css();

    }
}