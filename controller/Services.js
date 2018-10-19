var service = angular.module('ngApp.services', [])
.factory("ServerService",function($http){
  return{
        checkUsername: function (username,userid) {
            var param = {
				'username' : username,
				'userid' : userid
			}
			var my_url = url_checkusername+$.param(param);	
			return $http.get(my_url);
		},
		getNormalNoti: function (userid,usertype) {
            var param = {
				'userid' : userid,
				'usertype' : usertype,
				'instid' : instid
			}
			var my_url = url_getnormalnoti+$.param(param);	
			return $http.get(my_url);
		},
		getIndivisualNoti: function (userid,usertype) {
            var param = {
				'userid' : userid,
				'usertype' : usertype,
				'instid' : instid
			}
			var my_url = url_getindivisualnoti+$.param(param);	
			return $http.get(my_url);
		},
		usernameValidate: function (username) {
            if (!username) {
                return;
			} else if (username.length < 6 || username.length > 15) {
                return "Username must be min 6 and max 15 characters long";
			} else if (username.match(' ')) {
                return "Username don't allow blank space";
			} else if (!username.match(/[a-z]/)) {
                return "Username must have at least one small alphabet letter";
            }
            return true;
		}
    }
})