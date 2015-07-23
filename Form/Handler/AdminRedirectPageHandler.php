<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Form\Handler;

use Fulgurio\LightCMSBundle\Form\Handler\AdminPageHandler;
use Fulgurio\LightCMSBundle\Entity\Page;

class AdminRedirectPageHandler extends AdminPageHandler
{
    /**
     * Update page metas
     *
     * @param Page $page
     * @param array $data
     */
    protected function updatePageMetas(Page $page, array $data)
    {
        parent::updatePageMetas($page, $data);
        $em = $this->doctrine->getManager();
        $em->persist($this->initMetaEntity($page, 'target_link', $data['target_link']));
    }
}