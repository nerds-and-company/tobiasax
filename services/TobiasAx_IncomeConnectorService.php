<?php

namespace Craft;

/**
 * Tobias AX income connector service
 */
class TobiasAx_IncomeConnectorService extends TobiasAx_ConnectorService
{

    /**
     * Updates an income
     * @param TobiasAx_IncomeModel $income
     * @return GuzzleClient
     */
    public function updateIncome($income)
    {
        $data = [
            'income' => $income->getUpdateAttributes()
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'UpdateIncome',
            'tobiasax/templates/soap/income/update',
            $data
        );

        return $request;
    }
}
