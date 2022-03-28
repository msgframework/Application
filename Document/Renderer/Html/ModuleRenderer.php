<?php
namespace Msgframework\Lib\Document\Renderer\Html;

use Msgframework\Lib\Document\DocumentRenderer;
use RocketCMS\Lib\Extension\Module;
use RocketCMS\Lib\Extension\Module\ModuleSite;

/**
 * HTML document renderer for a single module
 *
 * @since  1.1.0
 */
class ModuleRenderer extends DocumentRenderer
{
    /**
     * Renders a module script and returns the results as a string
     *
     * @param string|null $name The name of the module to render
     * @param array|null $params
     * @param string|null $content If present, module information from the buffer will be used
     *
     * @return  string  The output of the script
     *
     * @since  1.1.0
     */
	public function render(string $name, ?array $params = array(), ?string $content = null): string
	{
        if(isset($params['module']) && $params['module'] instanceof Module) {
            $module = $params['module'];
        } else {
            $app = $this->factory->getApplication();
            $module = $app->getExtensionByName('module', $name);
        }

        $site = new ModuleSite($module, 0, $module->getName(), $module->getTitle(), 0, array(), 1);

        $buffer = $site->render();
        $this->_doc->setBuffer($buffer, array('type' => 'module', 'name' => $site->getName()));

        return $buffer;
	}
}
