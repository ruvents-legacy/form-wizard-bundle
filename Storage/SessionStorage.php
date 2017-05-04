<?php

namespace Ruvents\FormWizardBundle\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorage extends AbstractStorage
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key)
    {
        return $this->session->get($key, []);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, array $data)
    {
        $this->session->set($key, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($key)
    {
        $this->session->remove($key);
    }
}
