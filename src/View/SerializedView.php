<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\View;

use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use RuntimeException;

/**
 * Parent class for view classes generating serialized outputs like JsonView and XmlView.
 */
abstract class SerializedView extends View
{
    /**
     * Response type.
     *
     * @var string
     */
    protected $_responseType;

    /**
     * Default config options.
     *
     * Use ViewBuilder::setOption()/setOptions() in your controlle to set these options.
     *
     * - `serialize`: Option to convert a set of view variables into a serialized response.
     *   Its value can be a string for single variable name or array for multiple
     *   names. If true all view variables will be serialized. If null or false
     *   normal view template will be rendered.
     *
     * @var array{serialize:string|bool|null}
     */
    protected $_defaultConfig = [
        'serialize' => null,
    ];

    /**
     * Constructor
     *
     * @param \Cake\Http\ServerRequest|null $request Request instance.
     * @param \Cake\Http\Response|null $response Response instance.
     * @param \Cake\Event\EventManager|null $eventManager EventManager instance.
     * @param array $viewOptions An array of view options
     */
    public function __construct(
        ?ServerRequest $request = null,
        ?Response $response = null,
        ?EventManager $eventManager = null,
        array $viewOptions = []
    ) {
        if ($response) {
            $response = $response->withType($this->_responseType);
        }

        parent::__construct($request, $response, $eventManager, $viewOptions);
    }

    /**
     * Load helpers only if serialization is disabled.
     *
     * @return $this
     */
    public function loadHelpers()
    {
        if (!$this->getConfig('serialize')) {
            parent::loadHelpers();
        }

        return $this;
    }

    /**
     * Serialize view vars.
     *
     * @param array|string $serialize The name(s) of the view variable(s) that
     *   need(s) to be serialized
     * @return string|false The serialized data or false.
     */
    abstract protected function _serialize($serialize);

    /**
     * Render view template or return serialized data.
     *
     * @param string|null $template The template being rendered.
     * @param string|null|false $layout The layout being rendered.
     * @return string The rendered view.
     */
    public function render(?string $template = null, $layout = null): string
    {
        $serialize = $this->getConfig('serialize', false);

        if ($serialize === true) {
            $options = array_map(
                function ($v) {
                    return '_' . $v;
                },
                array_keys($this->_defaultConfig)
            );

            $serialize = array_diff(
                array_keys($this->viewVars),
                $options
            );
        }
        if ($serialize !== false) {
            $result = $this->_serialize($serialize);
            if ($result === false) {
                throw new RuntimeException('Serialization of View data failed.');
            }

            return $result;
        }

        return parent::render($template, false);
    }
}
