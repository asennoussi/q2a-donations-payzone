<?php
class qa_donations_payzone_process {
    function match_request($request)
    {
        $parts=explode('/', $request);
        return $parts[0]=='process-donation';
    }

    function process_request($request)
    {
        file_put_contents('test.txt', file_get_contents('php://input'));
    }
}