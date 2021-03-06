<?php

namespace VoyagerBread\Traits;

use Illuminate\Support\Arr;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

trait BreadSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDataType();
        $this->createInputFields();
        $this->createMenuItem();
        $this->generatePermissions();
    }

    /**
     * Create a new data-type for the current bread
     *
     * @return void
     */
    public function createDataType()
    {
        $dataType = $this->dataType('name', $this->bread()['name']);
        if (!$dataType->exists || $this->bread()['rebuild_data_type']) {
            $dataType->fill(Arr::except($this->bread(), ['rebuild_data_type', 'rebuild_data_rows']))->save();
        }
    }

    /**
     * Create all the input fields specified in the
     * bread() method
     *
     * @return [type] [description]
     */
    public function createInputFields()
    {
        $productDataType = DataType::where('name', $this->bread()['name'])->firstOrFail();
        DataRow::where('data_type_id', $productDataType->id)->delete();
        collect($this->inputFields())->each(function ($field, $key) use ($productDataType) {
            $dataRow = $this->dataRow($productDataType, $key);
            if (!$dataRow->exists || $this->bread()['rebuild_data_rows']) {
                $dataRow->fill($field)->save();
            }
        });
    }

    /**
     * Create the new menu entry using the configuration
     * specified in the menuEntry() method. IF set to null
     * then no menu entry is going to be created
     *
     * @return [type] [description]
     */
    public function createMenuItem()
    {
        if (empty($this->menuEntry())) {
            return;
        }
        $menuEntry = collect($this->menuEntry());

        if (empty($menuEntry->menu_id)) {
            $menu = Menu::where('name', $menuEntry->get('role'))->firstOrFail();
            $menuEntry = $menuEntry->put('menu_id', $menu->id);
        }

        $menuItem = MenuItem::firstOrNew($menuEntry->only(['menu_id', 'title', 'url', 'route'])->toArray());
        if (!$menuItem->exists || $this->bread()['rebuild_data_rows']) {
            $menuItem->fill($menuEntry->only(['target', 'icon_class', 'color', 'parent_id', 'order'])->toArray())->save();
        }
    }

    /**
     * Generates admin permissions to the current
     * bread
     *
     * @return void
     */
    public function generatePermissions()
    {
        Permission::generateFor($this->bread()['name']);
    }

    /**
     * Find or create a new data-type
     *
     * @param  string $field Field name
     * @param  string $for   Bread name
     *
     * @return DataType::class
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }

    /**
     * Find or create a new data-row
     *
     * @param  string $type  Type name
     * @param  string $field Field name
     *
     * @return DataType::class
     */
    protected function dataRow($type, $field)
    {
        return DataRow::firstOrNew([
            'data_type_id' => $type->id,
            'field' => $field,
        ]);
    }
}
