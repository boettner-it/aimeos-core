<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage JQAdm
 */


namespace Aimeos\Client\JQAdm\Product;


/**
 * Default implementation of product JQAdm client.
 *
 * @package Client
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Client\JQAdm\Common\Client\Factory\Base
	implements \Aimeos\Client\JQAdm\Common\Client\Factory\Iface
{
	/** client/jqadm/product/standard/subparts
	 * List of JQAdm sub-clients rendered within the product section
	 *
	 * The output of the frontend is composed of the code generated by the JQAdm
	 * clients. Each JQAdm client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain JQAdm clients themselves and therefore a
	 * hierarchical tree of JQAdm clients is composed. Each JQAdm client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the JQAdm code generated by the parent is printed, then
	 * the JQAdm code of its sub-clients. The order of the JQAdm sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/jqadm/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/jqadm/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural JQAdm, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2016.01
	 * @category Developer
	 */
	private $subPartPath = 'client/jqadm/product/standard/subparts';
	private $subPartNames = array();


	/**
	 * Copies a resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	 */
	public function copy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/type' );

		$search = $typeManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.type.code' ) ) );

		$view = $this->getView();

		$item = $manager->getItem( $view->param( 'id' ) );
		$item->setCode( $item->getCode() . '_copy' );
		$item->setId( null );

		$view->item = $item;
		$view->itemTypes = $typeManager->searchItems( $search );

		$tplconf = 'client/jqadm/product/template-item';
		$default = 'product/item-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Creates a new resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	 */
	public function create()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/type' );

		$search = $typeManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.type.code' ) ) );

		$view = $this->getView();
		$view->item = $manager->createItem();
		$view->itemTypes = $typeManager->searchItems( $search );

		$tplconf = 'client/jqadm/product/template-item';
		$default = 'product/item-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	 */
	public function delete()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$manager->deleteItems( (array) $this->getView()->param( 'id' ) );
	}


	/**
	 * Returns a single resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	 */
	public function get()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/type' );

		$search = $typeManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.type.code' ) ) );

		$view = $this->getView();
		$view->item = $manager->getItem( $view->param( 'id' ) );
		$view->itemTypes = $typeManager->searchItems( $search );

		$tplconf = 'client/jqadm/product/template-item';
		$default = 'product/item-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Saves the data
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	 */
	public function save()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$item = $manager->createItem();
		$view = $this->getView();

		try
		{
			$item->fromArray( $view->param( 'item', array() ) );

			$config = array();
			$raw = $view->param( 'product.config', array() );

			if( isset( $raw['key'] ) )
			{
				foreach( (array) $raw['key'] as $idx => $key ) {
					$config[$key] = ( isset( $raw['val'][$idx] ) && $raw['val'][$idx] !== '' ? $raw['val'][$idx] : '' );
				}
			}

			$item->setConfig( $config );

			$manager->saveItem( $item, false );
		}
		catch( \Exception $e )
		{
			$typeManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/type' );

			$search = $typeManager->createSearch();
			$search->setSortations( array( $search->sort( '+', 'product.type.code' ) ) );

			$view->item = $item;
			$view->itemTypes = $typeManager->searchItems( $search );

			$tplconf = 'client/jqadm/product/template-item';
			$default = 'product/item-default.php';

			return $view->render( $view->config( $tplconf, $default ) );
		}
	}


	/**
	 * Returns a list of resource according to the conditions
	 *
	 * @return string HTML output to display
	 */
	public function search()
	{
		$view = $this->getView();
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$search = $this->initCriteria( $manager->createSearch(), $view->param() );

		$view->items = $manager->searchItems( $search );
		$view->filterOperators = $search->getOperators();
		$view->filterAttributes = $manager->getSearchAttributes();

		$tplconf = 'client/jqadm/product/template-list';
		$default = 'product/list-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** client/jqadm/product/decorators/excludes
		 * Excludes decorators added by the "common" option from the product JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  client/jqadm/product/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\JQAdm\Common\Decorator\*") added via
		 * "client/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see client/jqadm/common/decorators/default
		 * @see client/jqadm/product/decorators/global
		 * @see client/jqadm/product/decorators/local
		 */

		/** client/jqadm/product/decorators/global
		 * Adds a list of globally available decorators only to the product JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  client/jqadm/product/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see client/jqadm/common/decorators/default
		 * @see client/jqadm/product/decorators/excludes
		 * @see client/jqadm/product/decorators/local
		 */

		/** client/jqadm/product/decorators/local
		 * Adds a list of local decorators only to the product JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\JQAdm\Product\Decorator\*") around the JQAdm client.
		 *
		 *  client/jqadm/product/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\JQAdm\Product\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see client/jqadm/common/decorators/default
		 * @see client/jqadm/product/decorators/excludes
		 * @see client/jqadm/product/decorators/global
		 */
		return $this->createSubClient( 'product/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames()
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}
}