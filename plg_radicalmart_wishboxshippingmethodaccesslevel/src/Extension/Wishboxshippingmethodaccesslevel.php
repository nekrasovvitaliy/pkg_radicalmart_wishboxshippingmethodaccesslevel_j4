<?php
/**
 * @copyright   (c) 2013-2024 Nekrasov Vitaliy
 * @license     GNU General Public License version 2 or later
 */
namespace Joomla\Plugin\Radicalmart\Wishboxshippingmethodaccesslevel\Extension;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @since 1.0.0
 */
final class Wishboxshippingmethodaccesslevel extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Autoload the language file.
	 *
	 * @var boolean
	 *
	 * @since 1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * @inheritDoc
	 *
	 * @return string[]
	 *
	 * @since 1.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onRadicalMartPrepareMethodForm' => 'onRadicalMartPrepareMethodForm',
			'onRadicalMartAfterGetOrderShippingMethodsList' => 'onRadicalMartAfterGetOrderShippingMethodsList'
		];
	}

	/**
	 * Constructor.
	 *
	 * @param   DispatcherInterface  $dispatcher  The dispatcher
	 * @param   array                $config      An optional associative array of configuration settings
	 *
	 * @since   1.0.0
	 */
	public function __construct(DispatcherInterface $dispatcher, array $config)
	{
		parent::__construct($dispatcher, $config);
	}

	/**
	 * @param   Form                      $form     Form
	 * @param   array|CMSObject|Registry  $data     Data
	 * @param   array|CMSObject           $tmpData  Tmp data
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function onRadicalMartPrepareMethodForm(Form $form, array|CMSObject|Registry $data, array|CMSObject $tmpData): void
	{
		$form->loadFile(JPATH_SITE . '/plugins/' . $this->_type . '/' . $this->_name . '/forms/shippingmethod.xml');
	}

	/**
	 * @param   string  $context   Context
	 * @param   array   $methods   Methods
	 * @param   array   $formData  Data
	 * @param   array   $products  Products
	 * @param   array   $currency  Currency
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function onRadicalMartAfterGetOrderShippingMethodsList(
		string $context,
		array &$methods,
		array $formData,
		array $products,
		array $currency
	): void
	{
		$app = Factory::getApplication();
		$user = $app->getIdentity();
		$userAccessLevelIds = $user->getAuthorisedViewLevels();

		foreach ($methods as $k => $method)
		{
			$methodAccessLevelId = $method->params->get('wishboxshippingmethodacceslevel', 0);

			if (!in_array($methodAccessLevelId, $userAccessLevelIds))
			{
				unset($methods[$k]);
			}
		}
	}
}
