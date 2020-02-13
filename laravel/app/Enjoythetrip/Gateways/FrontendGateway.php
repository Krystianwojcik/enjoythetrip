<?php
/*
|--------------------------------------------------------------------------
| app/Enjoythetrip/Repositories/FrontendRepository.php *** Copyright netprogs.pl | available only at Udemy.com | further distribution is prohibited  ***
|--------------------------------------------------------------------------
*/

namespace App\Enjoythetrip\Gateways; 

use App\Enjoythetrip\Interfaces\FrontendRepositoryInterface; 


class FrontendGateway  {  
    public function __construct(FrontendRepositoryInterface $fR) {
        $this->fR = $fR;
    }

    public function searchCities($request)
    {
       $term = $request->input('term');
        $result = [];
       $queries = $this->fR->getSearchCities($term);
        foreach($queries as $query) {
            $result = ['id'=>$query->id, 'value'=>$query->name];
        }
        return $result;
    } 
    public function getSearchResults($request)
    {

        $request->flash(); // inputs for session for one request

        if( $request->input('city') != null)
        {
            
            $dayin = date('Y-m-d', strtotime($request->input('check_in'))); /* Lecture 19 */
            $dayout = date('Y-m-d', strtotime($request->input('check_out'))); /* Lecture 19 */

            $result = $this->fR->getSearchResults($request->input('city'));

            if($result)
            {

                /* Lecture 19 */
                foreach ($result->rooms as $k=>$room)
                {
                   if( (int) $request->input('room_size') > 0 )
                   {
                        if($room->room_size != $request->input('room_size'))
                        {
                            $result->rooms->forget($k);
                        }
                   }

                    foreach($room->reservations as $reservation)
                    {

                        if( $dayin >= $reservation->day_in
                            &&  $dayin <= $reservation->day_out
                        )
                        {
                            $result->rooms->forget($k);
                        }
                        elseif( $dayout >= $reservation->day_in
                            &&  $dayout <= $reservation->day_out
                        )
                        {
                            $result->rooms->forget($k);
                        }
                        elseif( $dayin <= $reservation->day_in
                            &&  $dayout >= $reservation->day_out
                        )
                        {
                            $result->rooms->forget($k);
                        }

                    }

                }

                /* Lecture 19 */
                if(count($result->rooms)> 0)
                return $result;  // filtered result
                else return false;

            }

        }
        
        return false;

    }

}


