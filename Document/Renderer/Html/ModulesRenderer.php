<?php
namespace Msgframework\Lib\Document\Renderer\Html;

use Joomla\Database\ParameterType;
use Msgframework\Lib\Document\DocumentRenderer;
use RocketCMS\Lib\Extension\Module;
use RocketCMS\Lib\Extension\Module\ModuleSite;

/**
 * HTML document renderer for a single module
 *
 * @since  1.1.0
 */
class ModulesRenderer extends DocumentRenderer
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
    public function render(?string $name, ?array $params = array(), ?string $content = null): string
    {
        $app = \Cms::getApplication();
        $sites = array();
        $applicationId = $app->getId();

        $router = $app->getRouter();
        $session = \Cms::getSession();

        $current = $router->current();

        $menu = $current->getMenu();
        $menuId = $menu->id;

        $cityId = $session->get('city_id');

        $db = \Cms::getContainer()->get('db');

        $query = $db->getQuery(true)
            ->select(array('m.id', 'm.extension_id', 'm.name', 'm.title', 'm.pages', 'm.params', 'm.status', 'mm.menu_id', 'mc.city_id'))
            ->from($db->quoteName('#__module', 'm'))
            ->join(
                'LEFT',
                $db->quoteName('#__module_menu', 'mm'),
                $db->quoteName('mm.module_id') . ' = ' . $db->quoteName('m.id')
            )
            ->join(
                'LEFT',
                $db->quoteName('#__module_city', 'mc'),
                $db->quoteName('mc.module_id') . ' = ' . $db->quoteName('m.id')
            )
            ->where(
                [
                    $db->quoteName('m.status') . ' = 1',
                    $db->quoteName('m.application_id') . ' = :applicationId',
                    $db->quoteName('m.name') . ' = :siteName'
                ]
            )
            ->bind(':siteName', $name, ParameterType::STRING)
            ->bind(':applicationId', $applicationId, ParameterType::INTEGER)
            ->extendWhere(
                'AND',
                [
                    $db->quoteName('mm.menu_id') . ' = :menuId',
                    $db->quoteName('mm.menu_id') . ' IS NULL',
                ],
                'OR'
            )
            ->bind(':menuId', $menuId, ParameterType::INTEGER)
            ->extendWhere(
                'AND',
                [
                    $db->quoteName('mc.city_id') . ' = :cityId',
                    $db->quoteName('mc.city_id') . ' IS NULL',
                ],
                'OR'
            )
            ->bind(':cityId', $cityId, ParameterType::INTEGER);
        $db->setQuery($query);

        foreach ($db->loadObjectList() as $item) {
            $item->params = json_decode($item->params, JSON_OBJECT_AS_ARRAY);
            $module = $app->getExtension('module', $item->extension_id);
            $sites[] = new ModuleSite($module, $item->id, $item->name, $item->title, $item->pages, $item->params, $item->status);
        }

        $buffer = '';

        foreach ($sites as $site) {
            $buffer .= $site->render();
        }
        $this->_doc->setBuffer($buffer, array('type' => 'module', 'name' => $name));

        return $buffer;
    }
}
