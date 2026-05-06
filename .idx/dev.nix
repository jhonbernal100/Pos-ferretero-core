{pkgs}: {
  channel = "stable-24.11";
  packages = [
    pkgs.php82
    pkgs.php82Packages.composer
    pkgs.nodejs_22
    pkgs.openssh
  ];
  env = {};
  idx = {
    extensions = [
      "google.gemini-cli-vscode-ide-companion"
    ];
    workspace = {
      onCreate = {
        default.openFiles = [ "README.md" "resources/views/welcome.blade.php" ];
      };
      onStart = {
        start-tunnel = "ssh -f -N -o StrictHostKeyChecking=no -L 127.0.0.1:3307:127.0.0.1:3306 -i ~/.ssh/id_rsa_pf admavanzas@server62.web-hosting.com -p 21098";
      };
    };
    previews = {
      enable = true;
      previews = {
        web = {
          command = ["php" "artisan" "serve" "--port" "$PORT" "--host" "0.0.0.0"];
          manager = "web";
        };
      };
    };
  };
}