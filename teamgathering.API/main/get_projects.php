<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->project_id) ){
  include '../_general/cors.php';
  die();
}

$token = ( isset($request->token ) ) ? $request->token : false;
$project_id = $request->project_id;


// $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzkxNTQ1MjEsImp0aSI6InpkQlwvcWNpQnRveVk4blZMMytEMStXbk9mQStCTEhMTDhvYWtcL3BlZG9JVT0iLCJpc3MiOiJodHRwczpcL1wvd3d3LnRlYW1nYXRoZXJpbmcuY29tIiwiaWF1ZHNzIjoiaHR0cHM6XC9cL3RlYW1nYXRoZXJpbmcuY29tIiwibmJmIjoxNTc5MTU0NTIxLCJleHAiOjE1ODE1NzM3MjEsImRhdGEiOnsicHJvZmlsZV9pZCI6IjEiLCJmaXJzdF9uYW1lIjoieHh4ampqIiwibGFzdF9uYW1lIjoiU2FyaWNjZGRkZCIsImVtYWlsIjoiYUBhLmNvbSJ9fQ._HrkiTrEdL47TUKfBTEGXoIF7dRBP6zWJ7HOxJSM87o";
// $project_id = 1;



$data = new stdClass();
$teams = [];
$profiles = [];

try {
  global $conn;
  mysqli_check();

  $clauseArray = [ $profile_id ];
  $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=?', 'i', $clauseArray );

  if(!!$row_profile['profile_id']){
    if($row_profile['profile_status'] == 'public'){

      $sql = "SELECT DISTINCT teams.team_id, 
      teams.team_name, 
      teams.team_description, 
      FROM projects 
        LEFT JOIN teams ON teams.project_id = projects.project_id
        WHERE projects.project_id = ".$project_id." ORDER BY teams.team_id, profiles_team.profiles_team_id";     
        
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $project_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();

      if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
          if( !in_array( $team_object, $teams ) ){                   
            $sql2 = "SELECT DISTINCT
            profiles_team.profile_team_status, 
            profiles_team.role, 
            profiles.profile_id, 
            profiles.first_name, 
            profiles.last_name
            FROM profiles 
              LEFT JOIN profiles_team ON profiles_team.profile_id = profiles.profile_id
              WHERE profiles_team.team_id = ".$row['team_id']." ORDER BY profiles.profile_id";     
              
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $stmt2->close();

            $profiles = [];

            if(!!$result2 && $result2->num_rows > 0){  
              while( $row2 = $result2->fetch_assoc() ){



                array_push($profiles, $row2);
              

              }
            }
            $teams->profiles = $profiles;
            array_push($projects, $teams);
          }
        }
      }
    }
  }else{
    $data->message = "page not found";
    status_return(404); 
    echo json_encode($data);
    $conn->close();
    return; 
  }





















  // teams.team_name, 
  // teams.team_name, 
  // teams.team_description, 
  // profiles_team.profiles_team_status, 
  // profiles_team.role, 
  // profiles_team.team_id, 
  // profiles.profile_id, 
  // profiles.first_name, 
  // profiles.last_name

  // $sql = "SELECT DISTINCT *, (SELECT COUNT(profiles_team.profiles_team_id) 
  // FROM profiles_team WHERE profiles_team.team_id = teams.team_id) as count
  // FROM projects 
  //   LEFT JOIN teams ON teams.project_id = projects.project_id
  //   LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
  //   LEFT JOIN profiles ON profiles.profile_id = profiles_team.profile_id
  //   WHERE projects.project_id=".$project_id." ORDER BY teams.team_id".

  // $stmt = $conn->prepare($sql);
  // $stmt->execute('i', $project_id);
  // $result = $stmt->get_result();
  // $stmt->close();











  // , (SELECT COUNT(profiles_team.profiles_team_id) 
  // FROM profiles_team WHERE profiles_team.team_id = teams.team_id) as count



//   $sql = "SELECT DISTINCT *
//   FROM projects 
//     LEFT JOIN teams ON teams.project_id = projects.project_id
//     LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
//     LEFT JOIN profiles ON profiles.profile_id = profiles_team.profile_id
//     WHERE ( projects.project_id=".$project_id." AND profiles.profile_id = "$profile_id" ) 
//     OR ( projects.project_id=".$project_id." AND projects.project_status = 'public' ) ORDER BY teams.team_id";

//   $stmt = $conn->prepare($sql);
//   $stmt->execute('i', $project_id);
//   $result = $stmt->get_result();
//   $stmt->close();

//   if(!!$result && $result->num_rows > 0){  
//     while( $row = $result->fetch_assoc() ){
//         $profile_object = new stdClass(); 
//         $team_object = new stdClass(); 
//         if( !!$row["profile_id"] ){ 
//             $profile_object->profile_id = $row['profile_id'];
//             $profile_object->first_name = $row['first_name'];
//             $profile_object->last_name = $row['last_name'];
//             $profile_object->role = $row['role'];
//             $profile_object->project_status = $row['project_status']; 
//             $profile_object->created_date = $row['created_date']; 
//             $profile_object->project_roll = ( $row['owner_id'] == $profile_id ) ? 'Owner': 'Member';
            
//             if( !in_array( $profile_object, $projects ) ){                   
//                 array_push($projects, $profile_object);
//             }
//         }
//         if( !!$row["team_id"] ){ 
//                 $team_object->team_id = $row['team_id'];
//                 $team_object->team_name = $row['team_name'];
//                 $team_object->profile_team_status = $row['profile_team_status'];
//                 $team_object->joined_date = $row['joined_date'];

//             if( !in_array( $team_object, $teams ) ){
//                 array_push($teams, $team_object);
//             }
//         }
//     }
//   }





// $sql = "SELECT DISTINCT (SELECT COUNT(profiles_team.profiles_team_id) 
//   FROM profiles_team WHERE profiles_team.team_id = teams.team_id) as count
//   FROM projects 
//     LEFT JOIN teams ON teams.project_id = projects.project_id
//     LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
//     LEFT JOIN profiles ON profiles.profile_id = profiles_team.profile_id
//     WHERE projects.project_id=1 ORDER BY teams.team_id

//     $stmt = $conn->prepare($sql);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $stmt->close();










//   if(!!$token){
//     $pro_info = returnTokenProfileId($token);
//     $profile_id = $pro_info->profile_id;
  
//     $sql = "SELECT * FROM projects ".$param;
//     $stmt = $conn->prepare($sql);
//     isset($request->param) ? $stmt->execute("i", $param) : $stmt->execute() ;
//     $result = $stmt->get_result();
//     $stmt->close();
//   }else{
//     $sql = "SELECT * FROM projects ".$param;
//     $stmt = $conn->prepare($sql);
//     isset($request->param) ? $stmt->execute("i", $param) : $stmt->execute() ;
//     $result = $stmt->get_result();
//     $stmt->close();
//   }

//   if(!!$result && $result->num_rows > 0){  
//     while( $row = $result->fetch_assoc() ){
//       status_return(200);
//       echo json_encode($data);
//       $conn->close();
//       return;
//     }
//   }else{

//   }




}catch (Exception $e ){
  status_return(500);
  echo($e->message);
  return;
}
?>