<?php
echo "function jsonCall(\$package, \$class, \$params){\r\n";
echo "\$protocol = \"http".(($_SERVER["HTTPS"] == "on")?"s":"")."\";\r\n";
echo "\$host = \"".$_SERVER["SERVER_NAME"]."\";\r\n";
echo "\$port = \"".$_SERVER["SERVER_PORT"]."\";\r\n";
echo "if((\$fp = fsockopen(\"".(($_SERVER["HTTPS"] == "on")?"ssl://":"")."\".\$host, \$port)) !== FALSE){\r\n";
echo "fputs(\$fp, \"POST /jsonp.php HTTP/1.0\\r\\n\");\r\n";
echo "fputs(\$fp, \"Host: \".\$host.\"\\r\\n\");\r\n";
echo "fputs(\$fp, \"User-Agent: CLAY-JSON-CALLER\\r\\n\");\r\n";
echo "\$data = \"\";\r\n";
echo "\$data .= \"&c=\".urlencode(\$package);\r\n";
echo "\$data .= \"&p=\".urlencode(\$class);\r\n";
echo "foreach(\$params as \$key => \$value){\r\n";
echo "\$data .= \"&\".urlencode(\$key).\"=\".urlencode(\$value);\r\n";
echo "}\r\n";
echo "fputs(\$fp, \"Content-Type: application/x-www-form-urlencoded\\r\\n\");\r\n";
echo "fputs(\$fp, \"Content-Length: \".strlen(\$data).\"\\r\\n\");\r\n";
echo "fputs(\$fp, \"\\r\\n\");\r\n";
echo "fputs(\$fp, \$data);\r\n";
echo "\$response = \"\";\r\n";
echo "while(!feof(\$fp)){\r\n";
echo "\$response .= fgets(\$fp, 4096);\r\n";
echo "}\r\n";
echo "fclose(\$fp);\r\n";
echo "\$result = explode(\"\\r\\n\\r\\n\", \$response, 2);\r\n";
echo "return \$result[1];\r\n";
echo "}\r\n";
echo "return null;\r\n";
echo "}\r\n";
?>
