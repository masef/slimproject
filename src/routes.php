<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    function dbQuery($conn,$strsql) {
        $r = array();
        $query = mysqli_query($conn, $strsql);
        if(is_object($query)){ while ($row = mysqli_fetch_assoc($query)){ $r[] = $row;} }
        else{ $r = null; }
        // mysqli_close($conn);
        return $r;
    }

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    /**
     * Api POST /getUser
     *
     * @Body  id|string
     * @Body  email|string
     */
    $app->post("/getUser", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "SELECT * FROM user";
        if($data_resp["id"] || $data_resp["email"]){ $sql .= " WHERE id='".$data_resp["id"]."' or email='".$data_resp["email"]."' "; }
        $sql .=" limit 1;";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    /**
     * Api POST /getListUser
     *
     * @Body  id|string
     * @Body  email|string
     */
    $app->post("/getListUser", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "SELECT * FROM user";
        if($data_resp["id"] || $data_resp["email"]){ $sql .= " WHERE id='".$data_resp["id"]."' or email='".$data_resp["email"]."' "; }
        $sql .=";";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    /**
     * Api POST /getCompany
     *
     * @Body  id|string
     */
    $app->post("/getCompany", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "SELECT * FROM company";
        if($data_resp["id"]){ $sql .= " WHERE id='".$data_resp["id"]."' "; }
        $sql .=" limit 1;";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    /**
     * Api POST /getListCompany
     *
     * @Body  id|string
     */
    $app->post("/getListCompany", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "SELECT * FROM company";
        if($data_resp["id"]){ $sql .= " WHERE id='".$data_resp["id"]."' "; }
        $sql .=";";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    /**
     * Api POST /getBudgetCompany
     *
     * @Body  id|string
     */
    $app->post("/getBudgetCompany", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "SELECT * FROM company_budget";
        if($data_resp["id"]){ $sql .= " WHERE id='".$data_resp["id"]."' "; }
        $sql .=" limit 1;";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    /**
     * Api POST /getListBudgetCompany
     *
     * @Body  id|string
     */
    $app->post("/getListBudgetCompany", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "SELECT * FROM company_budget";
        if($data_resp["id"]){ $sql .= " WHERE id='".$data_resp["id"]."' "; }
        $sql .=";";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    /**
     * Api POST /getLogTransaction
     */
    $app->post("/getLogTransaction", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "SELECT distinct
            concat(u.first_name,' ',u.last_name) as user_name,
            u.account as user_account,
            c.name as company_name,
            case 
                when tr.type = 'R' then 'reimburse' 
                when tr.type = 'C' then 'disburse'
                when tr.type = 'S' then 'close'
                else '' 
                end as transaction_type,
            tr.date as transaction_date,
            tr.amount as transaction_amount,
            cb.amount as remaining_amount
        from transaction as tr
            inner join user as u on tr.user_id = u.id
            inner join company as c on c.id = u.company_id
            inner join company_budget as cb on cb.company_id = c.id
        ;";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    /**
     * Api POST /createUser
     *
     * @Body  first_name|string
     * @Body  last_name|string
     * @Body  email|string
     * @Body  account|string
     * @Body  company_id|string
     */
    $app->post("/createUser", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "INSERT INTO user(first_name, last_name, email, account, company_id) 
            VALUES('".$data_resp["first_name"]."', '".$data_resp["last_name"]."', '".$data_resp["email"]."', '".$data_resp["account"]."', '".$data_resp["company_id"]."')
        ;";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /createCompany
     *
     * @Body  name|string
     * @Body  address|string
     */
    $app->post("/createCompany", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "INSERT INTO company(name, address) 
            VALUES('".$data_resp["name"]."', '".$data_resp["address"]."')
        ;";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /reimburse
     *
     * @Body  user_id|string
     * @Body  amount|string
     */
    $app->post("/reimburse", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        dbQuery($conDb,"INSERT INTO transaction(type, user_id, amount) 
            VALUES('R', '".$data_resp["user_id"]."', '".$data_resp["amount"]."')
        ;");
        $result = dbQuery($conDb,"SELECT count(cb.id) as hsl from company_budget as cb inner join user as u on cb.company_id = u.company_id where u.id='".$data_resp["user_id"]."';");
        if(intval($result[0]["hsl"])>0){
            dbQuery($conDb,"UPDATE company_budget SET
                    amount =  (cast(amount as double)-cast('".$data_resp["amount"]."' as double))
                where company_id = (select company_id from user where id='".$data_resp["user_id"]."' limit 1)
            ;");
        }
        else{
            dbQuery($conDb,"INSERT INTO company_budget(company_id,amount) 
                values((select company_id from user where id='".$data_resp["user_id"]."' limit 1),'".$data_resp["amount"]."')
            ;");
        }
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /disburse
     *
     * @Body  user_id|string
     * @Body  amount|string
     */
    $app->post("/disburse", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        dbQuery($conDb,"INSERT INTO transaction(type, user_id, amount) 
            VALUES('C', '".$data_resp["user_id"]."', '".$data_resp["amount"]."')
        ;");
        $result = dbQuery($conDb,"SELECT count(cb.id) as hsl from company_budget as cb inner join user as u on cb.company_id = u.company_id where u.id='".$data_resp["user_id"]."';");
        if(intval($result[0]["hsl"])>0){
            dbQuery($conDb,"UPDATE company_budget SET
                    amount =  (cast(amount as double)-cast('".$data_resp["amount"]."' as double))
                where company_id = (select company_id from user where id='".$data_resp["user_id"]."' limit 1)
            ;");
        }
        else{
            dbQuery($conDb,"INSERT INTO company_budget(company_id,amount) 
                values((select company_id from user where id='".$data_resp["user_id"]."' limit 1),'".$data_resp["amount"]."')
            ;");
        }
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /close
     *
     * @Body  user_id|string
     * @Body  amount|string
     */
    $app->post("/close", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        dbQuery($conDb,"INSERT INTO transaction(type, user_id, amount) 
            VALUES('S', '".$data_resp["user_id"]."', '".$data_resp["amount"]."')
        ;");
        $result = dbQuery($conDb,"SELECT count(cb.id) as hsl from company_budget as cb inner join user as u on cb.company_id = u.company_id where u.id='".$data_resp["user_id"]."';");
        if(intval($result[0]["hsl"])>0){
            dbQuery($conDb,"UPDATE company_budget SET
                    amount =  (cast(amount as double)+cast('".$data_resp["amount"]."' as double))
                where company_id = (select company_id from user where id='".$data_resp["user_id"]."' limit 1)
            ;");
        }
        else{
            dbQuery($conDb,"INSERT INTO company_budget(company_id,amount) 
                values((select company_id from user where id='".$data_resp["user_id"]."' limit 1),'".$data_resp["amount"]."')
            ;");
        }
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /updateCompany
     *
     * @Body  id|string
     * @Body  name|string
     * @Body  address|string
     */
    $app->post("/updateCompany", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "UPDATE company SET
                    name = case when '".$data_resp["name"]."' is null or '".$data_resp["name"]."' = '' then name else '".$data_resp["name"]."' end,
                    address = case when '".$data_resp["address"]."' is null or '".$data_resp["address"]."' = '' then address else '".$data_resp["address"]."' end
                WHERE id='".$data_resp["id"]."'
        ;";
        $result = dbQuery($this->db,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /updateUser
     *
     * @Body  id|string
     * @Body  first_name|string
     * @Body  last_name|string
     * @Body  account|string
     * @Body  company_id|string
     */
    $app->post("/updateUser", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $sql = "UPDATE user SET
                    first_name = case when '".$data_resp["first_name"]."' is null or '".$data_resp["first_name"]."' = '' then first_name else '".$data_resp["first_name"]."' end,
                    last_name = case when '".$data_resp["last_name"]."' is null or '".$data_resp["last_name"]."' = '' then last_name else '".$data_resp["last_name"]."' end,
                    account = case when '".$data_resp["account"]."' is null or '".$data_resp["account"]."' = '' then account else '".$data_resp["account"]."' end,
                    company_id = case when '".$data_resp["company_id"]."' is null or '".$data_resp["company_id"]."' = '' then company_id else '".$data_resp["company_id"]."' end
                WHERE id='".$data_resp["id"]."'
        ;";
        $result = dbQuery($conDb,$sql);
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /deleteCompany
     *
     * @Body  id|string
     */
    $app->post("/deleteCompany", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $result = dbQuery($conDb,"DELETE FROM company WHERE id='".$data_resp["id"]."';");
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });

    /**
     * Api POST /deleteUser
     *
     * @Body  id|string
     */
    $app->post("/deleteUser", function (Request $request, Response $response){
        $data_resp = $request->getParsedBody();
        $conDb = $this->db;
        $result = dbQuery($conDb,"DELETE FROM user WHERE id='".$data_resp["id"]."';");
        mysqli_close($conDb);
        return $response->withJson(["status" => "success"], 200);
    });
};
