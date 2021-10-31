<?php 


class Ceres_Roles {
  
  public $wpRoles = ['administrator' => 'Administrator',
                     'editor'        => 'Editor',
                     'author'        => 'Author',
                     'contributor'   => 'Contributor',
                     'subscriber'    => 'Subscriber',
  ];
  
  // See matrix at https://drive.google.com/file/d/1yQ0NRfeWfOTf8TWH3rbkEn9fEzhfDoxG/view 
  public $ceresRoles = ['ceres_student'    => 'Student', // equals author
                        'ceres_ta'         => 'Teaching Assistant', // inherits from administrator, then gets some removed
                        'ceres_site_owner' => 'Site Owner', // inherits from administrator, then gets some removed
                        'ceres_designer'   => 'Site Designer',
  ];
  
  public $ceresRolesCapabilities = [
      'ceres_student'    => [], // have to figure out if I can separate writing and nav/categories
      'ceres_ta'         => [], // inherits from Administrator, then has stuff removed below
      'ceres_site_owner' => [], // inherits from Administrator, then has stuff removed below
      'ceres_designer'   => [], // as ceres_student/author, but can hit navigation or categories
  ];
  
  // @TODO nail down whether these can change other peoples' roles?
  public $ceresCapabilitiesToRemove = [
      'ceres_site_owner'  => [], //site owner can't mess with plugins or themes
      'ceres_ta'          => [], //TA can't mess with pluging or theme
      'ceres_student'     => [], // have to figure out if I can separate writing and nav/categories
      'ceres_designer'    => [], // as ceres_student/author, but can hit navigation or categories
    
  ];
  
  
  
  public function install() {
    // changes only happen once, upon installation, so check if it's already
    // installed, maybe also check if anything has gone haywire
    
    foreach ($this->ceresRoles as $ceresRole => $ceresRoleDisplayNamae) {
      $this->addCeresRole($ceresRole, $ceresRoleDisplayName);
    }
    
    foreach ($this->ceresRoles as $ceresRole => $ceresCapabilitiesToRemove) {
      
    }
  }
  
  public function uninstall() {
    // need to restore all role/capability functionality
    // means I need to remember somewhere the original settings for anything I modify
    
  }
  
  public function activate() {
    // maybe this actually uses the install code?
    // have to research what install, uninstall, activate, and deactivate do
    
    
  }
  
  public function deactivate() {
    // this should figure out how to restore 'native' capabilities and roles
  }
  
  public function addCeresRole($roleName, $display_name, $capabilities) {
    if (! $this->roleExists($roleName)) {
      $this->setupRole($roleName); // @TODO I'm liking the idea of this method less and less
      
      //make WP do its thing
      add_role( $roleName, $display_name, $capabilities = array() );
      
    } else {
      throw new Exception("Role $roleName already exists");
    }
    

  }
  
  // No soup for you!
  public function removeCapabilityFromRole($role, $capability) {
    if (is_string($role)) {
      $roleObject = get_role($role);
      if (! $roleObject) {
        throw new Exception("(string) $role does not exist");
      }
    } else {
      $roleObject = $role;
    }
    
    $roleObject->remove_cap($capability);
  }
  
  public function addCapabilityToRole($role, $capability) {
    if (is_string($role)) {
      $roleObject = get_role($role);
      if (! $roleObject) {
        throw new Exception("string $role does not exist");
      }
    } else {
      $roleObject = $role;
    }
    
  }
  
  /**
   * Mostly a wrapper around WP's functionality
   * 
   * @param string|Object $role
   * @param string $user
   */
  
  public function hasRole($role, $userLogin = null) {
    if (is_string($role)) {
      // this could probably be more efficient
      $roleObject = get_role($role);
      if (! $roleObject) {
        throw new Exception("string $role does not exist");
      }
    } else {
      $roleObject = $role;
    }
    // if user isn't supplied, use the current user
    if ( is_null($userLogin) ) {
      $userObject = wp_get_current_user();
    }
    
    $userObject = wp_get_user_by('login', $userLogin);
    $roles = $userObject->roles;
    if (in_array($role, $roles)) {
      return true;
    }
    return false;
  }
  
  /**
   * Mostly a wrapper around WP's functionality
   * 
   * @param string $capability
   * @param string|WP user object $user
   */
  
  // @TODO: too much duplication/wrapping around native wp functions?
  // depends on where I want to call it, and how. a private method?
  public function hasCapability($capability, $userLogin = null) {
    //if user isn't supplied, use the current user.
    if (! $user) {
      $userObject = wp_get_current_user();
    } else {
      $userObject = wp_get_user_by('login', $userLogin);
    }
    
    return $userObject->has_cap($capability);
  }
  
  
  private function roleExists($roleName) {
    $roleObject = get_role( $roleName );
    if (! $roleObject ) {
      return false;
    }
    return true;
  }
  
  private function copyCapabilities($parentRoleName, $childRoleName) {
    // get the parent's capabilities as array
    $parentRoleObject = get_role($parentRoleName);
    if (is_null($parentRoleObject)) {
      throw new Exception("No role named $parentRoleName");
    }
    
    $childRoleObject = get_role($childRoleName);
    if (is_null($childRoleObject)) {
      throw new Exception("No role named $childRoleName");
    }
    
    foreach ($parentRoleObject->capabilities as $capability) {
      $childRoleObject->add_cap($capability);
    }
  }
}

