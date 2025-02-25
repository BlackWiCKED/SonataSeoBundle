<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\SeoBundle\Tests\Block\Social;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Test\BlockServiceTestCase;
use Sonata\BlockBundle\Util\OptionsResolver;
use Sonata\SeoBundle\Block\Social\FacebookLikeBoxBlockService;

final class FacebookLikeBoxBlockServiceTest extends BlockServiceTestCase
{
    public function testService()
    {
        $service = new FacebookLikeBoxBlockService(
            'sonata.block.service.facebook.like_box',
            $this->templating
        );

        $block = new Block();
        $block->setType('core.text');
        $block->setSettings([
            'url' => 'url_setting',
            'width' => 'width_setting',
            'height' => 'height_setting',
            'colorscheme' => 'colorscheme_setting',
            'show_faces' => 'show_faces_setting',
            'show_header' => 'show_header_setting',
            'show_posts' => 'show_posts_setting',
            'show_border' => 'show_border_setting',
        ]);

        $optionResolver = new OptionsResolver();
        $service->setDefaultSettings($optionResolver);

        $blockContext = new BlockContext($block, $optionResolver->resolve($block->getSettings()));

        $formMapper = $this->createMock(FormMapper::class, [], [], '', false);
        $formMapper->expects(static::exactly(2))->method('add');

        $service->buildCreateForm($formMapper, $block);
        $service->buildEditForm($formMapper, $block);

        $service->execute($blockContext);

        static::assertSame('url_setting', $this->templating->parameters['settings']['url']);
        static::assertSame('width_setting', $this->templating->parameters['settings']['width']);
        static::assertSame('height_setting', $this->templating->parameters['settings']['height']);
        static::assertSame('colorscheme_setting', $this->templating->parameters['settings']['colorscheme']);
        static::assertSame('show_faces_setting', $this->templating->parameters['settings']['show_faces']);
        static::assertSame('show_header_setting', $this->templating->parameters['settings']['show_header']);
        static::assertSame('show_posts_setting', $this->templating->parameters['settings']['show_posts']);
        static::assertSame('show_border_setting', $this->templating->parameters['settings']['show_border']);
    }
}
