﻿using NHibernate;

namespace SewebarKey.Repositories
{
    public interface ICommand
    {
        void Execute(ISession session);
    }
}