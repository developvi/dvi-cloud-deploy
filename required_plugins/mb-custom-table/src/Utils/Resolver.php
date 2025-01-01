<?php

namespace MetaBox\CustomTable\Utils;

/**
 * Resolve the callback paramters for class methods.
 * This helps user define the callback closure regardless the position of arguments.
 *
 * @since 1.0
 */
class Resolver {
	/**
	 * Bind parameter with the app variables.
	 *
	 * @var array
	 */
	protected $bindMaps;

	/**
	 * Bind parameter with the app variables.
	 *
	 * @param array $bind the key is the variable name and the value is the app variables.
	 *
	 * @return $this
	 */
	public function bind( array $bind = [] ) {
		$this->bindMaps = $bind;

		return $this;
	}

	/**
	 * Resolve the closure and do the magic here
	 *
	 * @param mixed $closure
	 *
	 * @return mixed
	 */
	public function resolve( $closure ) {
		$reflection = ( is_array( $closure ) ) ?
			new \ReflectionMethod( $closure[0], $closure[1] ) :
			new \ReflectionFunction( $closure );

		$arguments = $reflection->getParameters();

		$arguments = array_map( function ( $argument ) {
			return $this->bindMaps[ $argument->name ] ?? null;
		}, $arguments );

		return @call_user_func_array( $closure, $arguments );
	}
}
