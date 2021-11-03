<?php 


class CeresRoles {
  
  public $wpRoles = ['administrator' => 'Administrator',
                     'editor'        => 'Editor',
                     'author'        => 'Author',
                     'contributor'   => 'Contributor',
                     'subscriber'    => 'Subscriber',
  ];
  
  // See matrix at https://drive.google.com/file/d/1yQ0NRfeWfOTf8TWH3rbkEn9fEzhfDoxG/view 
  public $ceresRoles = ['ceres_student'      => 'CERES Student', // equals author
                        'ceres_site_manager' => 'CERES Site Manager', // inherits from administrator, then gets some removed
                        'ceres_site_owner'   => 'CERES Site Owner', // inherits from administrator, then gets some removed
                        'ceres_designer'     => 'CERES Site Designer',
  ];
  
  // Annoyingly looks like I have to set these via WP, and PHP doesn't seem to like
  // that here. See the install method
  public $ceresRolesCapabilities = [
      'ceres_student'      => [], // have to figure out if I can separate writing and nav/categories
      'ceres_site_manager' => [], // inherits from Administrator, then has stuff removed below
      'ceres_site_owner'   => [], // inherits from Administrator, then has stuff removed below
      'ceres_designer'     => [], // as ceres_student/author, but can hit navigation or categories
  ];
  
  // @TODO nail down whether these can change other peoples' roles?
  public $ceresCapabilitiesToRemove = [
      //site owner can't mess with plugins or themes
      'ceres_site_owner'   => [
          'activate_plugins',
          'switch_themes',
      ], 
      
      //site manager can't mess with plugins or theme
      'ceres_site_manager' => [
          'activate_plugins',
          'switch_themes',
      ],
      
      // inherit from author
      'ceres_student'      => [
          'edit_others_pages',
          'edit_others_posts',
          'manage_categories',
          'manage_links',
          'moderate_comments',   
          
      ],
      
      // as ceres_student/author, but can hit navigation or categories
      'ceres_designer'     => [
      ], 
  ];
  
  
  
  public function install() {
    
    // changes only happen once, upon installation, so check if it's already
    // installed, maybe also check if anything has gone haywire
    $this->ceresRolesCapabilities['ceres_site_owner'] = get_role( 'administrator' )->capabilities;
    $this->ceresRolesCapabilities['ceres_site_manager'] = $this->ceresRolesCapabilities['ceres_site_owner'];
    $this->ceresRolesCapabilities['ceres_student'] =  get_role( 'author' )->capabilities;
    $this->ceresRolesCapabilities['ceres_designer'] =  get_role( 'author' )->capabilities;;
    
    foreach ($this->ceresRoles as $ceresRole => $ceresRoleDisplayName) {
      $this->addCeresRole($ceresRole, $ceresRoleDisplayName, $this->ceresRolesCapabilities[$ceresRole]);
    }
    
    // set the inheritance of capabilities for ceresRoles
    // Site Owner from Administrator
    // Site Manager from Administrator
    // Designer from Author
    // Student from Author (if we keep it -- note as of Oct 31 2021. Spooky!)
    
    foreach ($this->ceresRoles as $ceresRole => $ceresCapabilityToRemove) {
      $this->removeCapabilityFromRole($ceresRole, $ceresCapabilityToRemove);
    }
  }
  
  public function uninstall() {
   // just need to remove the roles, since current plan is only to inherit
   // then modifying
  }
  
  public function deactivate() {
    // this should figure out how to restore 'native' capabilities and roles
  }
  
  public function addCeresRole($roleName, $display_name, $capabilities) {
    if (! $this->roleExists($roleName)) {
      
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

