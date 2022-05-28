<?php

namespace Framework\Baseapp\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Presenter\ModelFractalPresenter;
use Swoolecan\Foundation\Models\TraitModel;
use Framework\Baseapp\Helpers\ResourceContainer;

/**
 * Class AbstractModel
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Models
 * @license https://opensource.org/licenses/MIT MIT
 * @mixin \Eloquent
 */
class AbstractModel extends Model
{
    use TraitModel;
    public $config;

    public function getResource()
    {
        return app(ResourceContainer::class);
    }

    public function getColumnElems($type = 'keyValue')
    {
        //$results = $this->getConnection()->getSchemaBuilder()->getColumnType($this->getTable());
        //$results = \Schema::getColumnListing($this->getTable());
        //$results = $this->getConnection()->getDoctrineSchemaManager()->listTableColumns($this->getTable());
        //$results = $this->getConnection()->getDoctrineSchemaManager()->listTableDetails($this->getTable())->getColumn('code');;
        $results = $this->getConnection()->getDoctrineSchemaManager()->listTableColumns($this->getConnection()->getTablePrefix() . $this->getTable());
        $datas = [];
        if ($type == 'keyValue') {
            $datas = [];
            foreach ($results as $result) {
                $datas[$result->getName()] = $result->getComment();//empty($result['column_comment']) ? $result['column_name'] : $result['column_comment'];
            }
            return $datas;
        }
        return $results;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->config = config();
        //var_dump(get_class($this->config));
    }

    /**
     * Set Model Presenter.
     *
     * @return $this
     * @throws \Exception
     */
    public function setModelPresenter()
    {
        $this->setPresenter(new ModelFractalPresenter());

        return $this;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key The Attribute Name
     * @param mixed $value The Attribute Value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->timestampAlwaysUtc) {
            // set to UTC only if Carbon
            if ($value instanceof Carbon) {
                $value->setTimezone('UTC');
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param string $key The Attribute Name
     *
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if ($value instanceof Carbon && $this->getWithUserTimezone) {
            $value->setTimezone($this->getAuthUserDateTimezone());
        }

        return $value;
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date DateTime Interface
     *
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        if ($date instanceof Carbon && $this->getWithUserTimezone) {
            $date->setTimezone($this->getAuthUserDateTimezone());
        }

        return $date->format($this->getDateFormat());
    }

    public function preInfo($params)
    {
        $params['orderBy'] = $params['orderBy'] ?? ['id' => 'desc'];
        $result = $this->_relateDatas(1, $params);
        return $result->isEmpty() ? [] : $result[0];
    }

    public function nextInfo($params)
    {
        $params['orderBy'] = $params['orderBy'] ?? ['id' => 'asc'];
        $result = $this->_relateDatas(1, $params);
        return $result->isEmpty() ? [] : $result[0];
    }

    public function relateDatas($num, $params)
    {
        return $this->_relateDatas($num, $params);
    }

    public function _relateDatas($num, $params)
    {
        $where = $params['where'] ?? [];
        $orderBy = $params['orderBy'] ?? ['id' => 'desc'];
        $select = $params['select'] ?? 'id,name,description,created_at';
        $datas = $this->query()->where($where)->limit($num)->get();
        return $datas;
    }

    /**
     * Get ID from the model primary key.
     *
     * @return mixed
     */
    /*public function getIdAttribute()
    {
        return $this->attributes[$this->getKeyName()];
    }*/
}
