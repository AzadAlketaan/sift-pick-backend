// Pages/Users.jsx
import React from 'react';

const Users = ({ users }) => {
    return (
        <div>
            <h1>Users</h1>
            <ul>
                {users.map(user => (
                    <li key={user.id}>{user.email}</li>
                ))}
            </ul>
        </div>
    );
};

export default Users;