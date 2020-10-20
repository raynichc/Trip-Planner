<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

include "./modules/Trip Planner/moduleFunctions.php";
include "./modules/Trip Planner/src/Domain/TripPlanner/TripGateway.php";

use Gibbon\Forms\Form;
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\TripPlanner\Domain\TripGateway;

if (!isActionAccessible($guid, $connection2, '/modules/Trip Planner/trips_reportToday.php')) {
    print "<div class='error'>";
        print "You do not have access to this action.";
    print "</div>";
} else {
    $page->breadcrumbs->add( _('Today\'s Trips'));

    $gateway = $container->get(TripGateway::class);
    $criteria = $gateway
      ->newQueryCriteria(true)
      ->filterBy('tripDay',date('Y-m-d'))
      ->filterBy('status',serialize([
        'Requested',
        'Approved',
        'Awaiting Final Approval'
      ]));
    $trips = $gateway->queryTrips($criteria);

    $table = DataTable::createPaginated('report',$criteria);
    $table->setTitle(__("Today's Trips"));
    $table->addColumn('tripTitle',__('Title'));
    $table->addColumn('description',__('Description'));
    $table
      ->addColumn('owner',__('Owner'))
      ->format(function($row) {
        return Format::name($row['title'],$row['preferredName'],$row['surname']);
      });
    $table->addColumn('status',__('Status'));
    $table->addActionColumn()
        ->addParam('tripPlannerRequestID')
        ->format(function($row,$actions) {
          $actions
            ->addAction('view',__('View'))
            ->setURL('/modules/Trip Planner/trips_requestView.php');
        });
    echo $table->render($trips); 
}
?>
