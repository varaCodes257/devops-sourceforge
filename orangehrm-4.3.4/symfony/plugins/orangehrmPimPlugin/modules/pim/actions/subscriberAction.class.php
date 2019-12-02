<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
class subscriberAction extends basePimAction {

    /**
     * @var EmployeeService
     */
    private $employeeService = null;


    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService(): EmployeeService {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }

        return $this->employeeService;
    }

    /**
     * @param $request
     *
     * @throws Exception
     */
    public function execute($request) {
        $request->setParameter('initialActionName', '');

        $subscriberService = new EmployeeSubscriptionService();
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();

        if (empty($loggedInEmpNum)) {
            $this->forwardToSecureAction();
        }

        $this->isSubscribed = $subscriberService->isSubscribed($loggedInEmpNum);
        $this->form = new EmployeeSubscriberForm(array(), ['empNumber' => $loggedInEmpNum], true);

        $this->empNumber = $loggedInEmpNum;

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $data = $this->form->getValues();

                $subscriberService->subscribe(
                    $loggedInEmpNum,
                    $data['name'], $data['email']
                );
                $this->isSubscribed = true;
            }
        }
    }

}

