<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BreadCrumbs
{
    public function make(array $breadcrumbs)
    {
        $title = [];
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $title[] = ucwords(str_replace('-', ' ', $breadcrumb));
        }

        $breadcrumbs_link = [];
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumb_implode = array_slice($breadcrumbs, 0, $index + 1);
            $breadcrumb_implode = implode('/', $breadcrumb_implode);
            $breadcrumbs_link[$index]['url'] = URL::to('/' . $breadcrumb_implode);
            $breadcrumbs_link[$index]['title'] = $title[$index];
        }
        return $breadcrumbs_link;
    }

    public function trans(array $breadcrumbs)
    {
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbs[$index] = Str::slug($breadcrumb);
        }
        return $breadcrumbs;
    }
}
