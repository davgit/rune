<?php
namespace uuf6429\Prune\Context;

use uuf6429\Prune\Action\AbstractAction;
use uuf6429\Prune\Rule\AbstractRule;
use uuf6429\Prune\Util\ContextField;
use uuf6429\Prune\Util\Evaluator;
use uuf6429\Prune\Util\TypeAnalyser;
use uuf6429\Prune\Util\TypeInfo;

/**
 * When implementing your own context:
 * - override the constructor for DI but make sure to call parent!
 * - implement getFieldList() to return an array of fields available to context.
 * - implement getValueList() to return an array of (field-name => field-value),
 *   possibly using data injected via constructor.
 * IMPORTANT: Keep into consideration that context can be used for read-only
 * purposes and therefore no data is provided to constructor!
 * In such a case, getValueList() should return an empty array.
 */
abstract class AbstractContext
{
    /**
     * @var AbstractAction|null
     */
    protected $action;

    /**
     * @var ContextField[]
     */
    private $fields = [];

    /**
     * @param AbstractAction|null $action
     */
    public function __construct($action = null)
    {
        $this->action = $action;
        $fields = $this->getFieldList();
        $values = $this->getValueList();

        foreach ($fields as $field) {
            $fieldName = $field->getName();

            if (isset($values[$fieldName])) {
                $field->setValue($values[$fieldName]);
            }

            if (isset($this->fields[$fieldName])) {
                throw new \LogicException(sprintf(
                    'Field named %s already added to field list.',
                    $fieldName
                ));
            }

            $this->fields[$fieldName] = $field;
        }
    }

    /**
     * @return ContextField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return TypeInfo[string]
     */
    public function getTypeInfo()
    {
        $analyser = new TypeAnalyser();
        foreach ($this->getFields() as $field) {
            foreach ($field->getTypes() as $type) {
                $analyser->analyse($type);
            }
        }

        return $analyser->getTypes();
    }

    /**
     * @param Evaluator $eval
     * @param AbstractRule $rule
     */
    public function execute(Evaluator $eval, AbstractRule $rule)
    {
        if ($this->action !== null) {
            $this->action->execute($eval, $this, $rule);
        }
    }

    /**
     * @return ContextField[]
     */
    abstract protected function getFieldList();

    /**
     * @return mixed[string]
     */
    abstract protected function getValueList();
}
