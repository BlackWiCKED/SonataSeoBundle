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

namespace Sonata\SeoBundle\Block\Social;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Facebook like button integration.
 *
 * @see https://developers.facebook.com/docs/plugins/like-button/
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 *
 * @deprecated since sonata-project/seo-bundle 2.14, to be removed in 3.0.
 */
class FacebookLikeButtonBlockService extends BaseFacebookSocialPluginsBlockService
{
    /**
     * @var string[]
     */
    protected $layoutList = [
        'standard' => 'form.label_layout_standard',
        'box_count' => 'form.label_layout_box_count',
        'button_count' => 'form.label_layout_button_count',
        'button' => 'form.label_layout_button',
    ];

    /**
     * @var string[]
     */
    protected $actionTypes = [
        'like' => 'form.label_action_like',
        'recommend' => 'form.label_action_recommend',
    ];

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => '@SonataSeo/Block/block_facebook_like_button.html.twig',
            'url' => null,
            'width' => null,
            'show_faces' => true,
            'share' => true,
            'layout' => $this->layoutList['standard'],
            'colorscheme' => $this->colorschemeList['light'],
            'action' => $this->actionTypes['like'],
        ]);
    }

    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        $form->add('settings', ImmutableArrayType::class, [
            'keys' => [
                ['url', UrlType::class, [
                    'required' => false,
                    'label' => 'form.label_url',
                ]],
                ['width', IntegerType::class, [
                    'required' => false,
                    'label' => 'form.label_width',
                ]],
                ['show_faces', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.label_show_faces',
                ]],
                ['share', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.label_share',
                ]],
                ['layout', ChoiceType::class, [
                    'required' => true,
                    'choices' => $this->layoutList,
                    'label' => 'form.label_layout',
                ]],
                ['colorscheme', ChoiceType::class, [
                    'required' => true,
                    'choices' => $this->colorschemeList,
                    'label' => 'form.label_colorscheme',
                ]],
                ['action', ChoiceType::class, [
                    'required' => true,
                    'choices' => $this->actionTypes,
                    'label' => 'form.label_action',
                ]],
            ],
            'translation_domain' => 'SonataSeoBundle',
        ]);
    }

    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (null !== $code ? $code : $this->getName()), false, 'SonataSeoBundle', [
            'class' => 'fa fa-facebook-official',
        ]);
    }
}
