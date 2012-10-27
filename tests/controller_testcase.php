<?php

abstract class Controller_TestCase extends PHPUnit_Framework_TestCase
{
    public function call($destination, $parameters = array(), $method = 'GET')
    {
        \Laravel\Request::foundation()->server->add(array(
            'REQUEST_METHOD' => $method,
        ));

        return \Laravel\Routing\Controller::call($destination, $parameters);
    }

    public function get($destination, $parameters = array())
    {
        return $this->call($destination, $parameters, 'GET');
    }

    public function post($destination, $post_data, $parameters = array())
    {
        $this->flush();

        \Laravel\Request::foundation()->request->add($post_data);

        return $this->call($destination, $parameters, 'POST');
    }

    private function flush()
    {
        $request = \Laravel\Request::foundation()->request;

        foreach ($request->keys() as $key)
        {
            $request->remove($key);
        }
    }
}